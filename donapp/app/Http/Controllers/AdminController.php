<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Donacion;
use App\Models\Solicitud;
use App\Models\Categoria;
use App\Models\Evento;
use App\Models\Publicacion;
use App\Models\ProgramadorEventos;
use App\Mail\NotificacionEstado;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $idAdmin = $request->session()->get('usuario.idUsuario');

        // ── Usuarios ──────────────────────────────────────────────────────────
        $usuarios = Usuario::when($request->search, fn($q, $s) =>
                $q->where('nombre', 'like', "%$s%")->orWhere('email', 'like', "%$s%")
            )
            ->when($request->rol, fn($q, $r) => $q->where('rol', $r))
            ->when($request->prioridad, fn($q, $p) => $q->where('prioridad', $p))
            ->orderByDesc('idUsuario')->get();

        // ── Categorías ────────────────────────────────────────────────────────
        $categorias = Categoria::with('creadaPor')
            ->withCount(['donaciones', 'solicitudes'])
            ->when($request->cat_search, fn($q, $s) => $q->where('nombre', 'like', "%$s%"))
            ->orderBy('nombre')->get();

        // ── Donaciones ────────────────────────────────────────────────────────
        $donaciones = Donacion::with(['categoria', 'donantes'])
            ->when($request->don_search, fn($q, $s) =>
                $q->where('descripcion', 'like', "%$s%")
                  ->orWhereHas('donantes', fn($q2) => $q2->where('nombre', 'like', "%$s%"))
            )
            ->when($request->don_estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->don_cat, fn($q, $c) => $c ? $q->where('idCategoria', $c) : $q)
            ->orderByDesc('idDonacion')->get();

        // ── Solicitudes ───────────────────────────────────────────────────────
        $solicitudes = Solicitud::with(['categoria', 'solicitante', 'gestor'])
            ->when($request->sol_search, fn($q, $s) =>
                $q->where('descripcion', 'like', "%$s%")
                  ->orWhereHas('solicitante', fn($q2) => $q2->where('nombre', 'like', "%$s%"))
            )
            ->when($request->sol_estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->sol_cat, fn($q, $c) => $c ? $q->where('idCategoria', $c) : $q)
            ->orderByDesc('idSolicitud')->get();

        // ── Eventos ───────────────────────────────────────────────────────────
        $eventos = Evento::with(['publicacion.autor', 'programacion'])
            ->when($request->ev_search, fn($q, $s) => $q->where('Nombre', 'like', "%$s%"))
            ->when($request->ev_estado, fn($q, $e) => $q->where('estado', $e))
            ->orderByDesc('idEvento')->get();

        // ── Estadísticas del dashboard ────────────────────────────────────────
        $totalUsuarios   = Usuario::count();
        $totalDonaciones = Donacion::count();
        $totalSolicitudes= Solicitud::count();
        $totalEventos    = Evento::count();
        $totalAprobadas  = Donacion::where('estado', 'aprobada')->count();
        $totalCategorias = Categoria::count();
        $adminActual     = Usuario::findOrFail($idAdmin);

        // ── Datos para reportes PDF (JSON) ────────────────────────────────────
        $donacionesRpt = Donacion::with(['categoria', 'donantes'])
    ->orderByDesc('idDonacion')->get()
    ->map(fn($d) => [
        'idDonacion'    => $d->idDonacion,
        'descripcion'   => $d->descripcion,
        'categoria'     => $d->categoria?->nombre ?? '—',
        'stock'         => $d->stock,
        'estado'        => $d->estado,
        'fechaCreacion' => $d->donantes->first()?->pivot?->FechaCreacion ?? null,
        'donante'       => $d->donantes->first()?->nombre ?? '—',
        'observacion'   => $d->observacion ?? '',
    ])->toArray();

$solicitudesRpt = Solicitud::with(['categoria', 'solicitante'])
    ->orderByDesc('idSolicitud')->get()
    ->map(fn($s) => [
        'idSolicitud'   => $s->idSolicitud,
        'descripcion'   => $s->descripcion,
        'categoria'     => $s->categoria?->nombre ?? '—',
        'estado'        => $s->estado,
        'fechaCreacion' => $s->fechaCreacion ?? null,
        'solicitante'   => $s->solicitante?->nombre ?? '—',
        'observacion'   => $s->observacion ?? '',
    ])->toArray();

        return view('admin.dashboard', compact(
            'usuarios', 'categorias', 'donaciones', 'solicitudes', 'eventos',
            'totalUsuarios', 'totalDonaciones', 'totalSolicitudes', 'totalEventos',
            'totalAprobadas', 'totalCategorias', 'adminActual',
            'donacionesRpt', 'solicitudesRpt'
        ));
    }

    // ── USUARIOS ──────────────────────────────────────────────────────────────

    public function crearUsuario(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'         => 'required|min:3|max:100',
            'tipoDocumento'  => 'required',
            'numDocumento'   => 'required|numeric|unique:usuario',
            'fechaNacimiento'=> 'required|date',
            'direccion'      => 'required|min:5',
            'email'          => 'required|email|unique:usuario',
            'telefono'       => 'required|digits:10',
            'rol'            => 'required|in:administrador,asistente,donante',
            'estado'         => 'required|in:activo,inactivo',
            'password'       => 'required|min:6|confirmed',
        ]);

        $rol = $request->rol;
        Usuario::create([
            'nombre'             => $request->nombre,
            'tipoDocumento'      => $request->tipoDocumento,
            'numDocumento'       => $request->numDocumento,
            'fechaNacimiento'    => $request->fechaNacimiento,
            'direccion'          => $request->direccion,
            'email'              => $request->email,
            'contrasena'         => Hash::make($request->password),
            'telefono'           => $request->telefono,
            'rol'                => $rol,
            'estado'             => $request->estado,
            'necesidad'          => $rol === 'donante' ? ($request->necesidad ?: null) : null,
            'prioridad'          => $rol === 'donante' ? ($request->prioridad ?: null) : null,
            'observacion_visita' => $rol === 'donante' ? ($request->observacion_visita ?: null) : null,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'usuarios'])->with('success', 'Usuario creado correctamente.');
    }

    public function editarUsuario(Request $request, int $id): RedirectResponse
    {
        $sesionId = $request->session()->get('usuario.idUsuario');
        $rules = [
            'nombre'         => 'required|min:3|max:100',
            'tipoDocumento'  => 'required',
            'numDocumento'   => "required|numeric|unique:usuario,numDocumento,$id,idUsuario",
            'fechaNacimiento'=> 'required|date',
            'direccion'      => 'required|min:5',
            'email'          => "required|email|unique:usuario,email,$id,idUsuario",
            'telefono'       => 'required|digits:10',
            'rol'            => 'required|in:administrador,asistente,donante',
            'estado'         => 'required|in:activo,inactivo',
        ];
        if ($request->filled('password')) $rules['password'] = 'min:6|confirmed';
        $request->validate($rules);

        if ($id === $sesionId) {
            if ($request->rol !== $request->session()->get('usuario.rol'))
                return back()->withErrors(['msg' => 'No puedes cambiar tu propio rol.']);
            if ($request->estado !== 'activo')
                return back()->withErrors(['msg' => 'No puedes desactivar tu propia cuenta.']);
        }

        $usuario = Usuario::findOrFail($id);
        $rol     = $request->rol;
        $data = [
            'nombre'             => $request->nombre,
            'tipoDocumento'      => $request->tipoDocumento,
            'numDocumento'       => $request->numDocumento,
            'fechaNacimiento'    => $request->fechaNacimiento,
            'direccion'          => $request->direccion,
            'email'              => $request->email,
            'telefono'           => $request->telefono,
            'rol'                => $rol,
            'estado'             => $request->estado,
            'necesidad'          => $rol === 'donante' ? ($request->necesidad ?: null) : null,
            'prioridad'          => $rol === 'donante' ? ($request->prioridad ?: null) : null,
            'observacion_visita' => $rol === 'donante' ? ($request->observacion_visita ?: null) : null,
        ];
        if ($request->filled('password')) $data['contrasena'] = Hash::make($request->password);
        $usuario->update($data);

        return redirect()->route('admin.dashboard', ['tab' => 'usuarios'])->with('success', 'Usuario actualizado.');
    }

    public function toggleEstado(Request $request, int $id): RedirectResponse
    {
        if ($id === $request->session()->get('usuario.idUsuario'))
            return back()->with('error', 'No puedes cambiar tu propio estado.');
        $usuario = Usuario::findOrFail($id);
        $nuevo   = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
        $usuario->update(['estado' => $nuevo]);
        return redirect()->route('admin.dashboard', ['tab' => 'usuarios'])
            ->with('success', "Usuario $nuevo correctamente.");
    }

    public function actualizarPerfil(Request $request): RedirectResponse
    {
        $id = $request->session()->get('usuario.idUsuario');
        $request->validate([
            'nombre'         => 'required|min:3|max:100',
            'tipoDocumento'  => 'required',
            'numDocumento'   => "required|numeric|unique:usuario,numDocumento,$id,idUsuario",
            'fechaNacimiento'=> 'required|date',
            'direccion'      => 'required|min:5',
            'email'          => "required|email|unique:usuario,email,$id,idUsuario",
            'telefono'       => 'required|digits:10',
        ]);
        $usuario = Usuario::findOrFail($id);
        $data    = $request->only(['nombre','tipoDocumento','numDocumento','fechaNacimiento','direccion','email','telefono']);
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $data['contrasena'] = Hash::make($request->password);
        }
        $usuario->update($data);
        $request->session()->put('usuario.nombre', $request->nombre);
        return redirect()->route('admin.dashboard', ['tab' => 'perfil'])->with('success', 'Perfil actualizado.');
    }

    // ── CATEGORÍAS ────────────────────────────────────────────────────────────

    public function crearCategoria(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre_categoria' => 'required|min:3|regex:/^[A-Za-záéíóúÁÉÍÓÚñÑüÜ\s\(\)\-]+$/u|unique:categoria,nombre',
        ], ['nombre_categoria.unique' => 'Ya existe una categoría con ese nombre.']);
        Categoria::create(['nombre' => $request->nombre_categoria, 'idUsuario' => $request->session()->get('usuario.idUsuario')]);
        return redirect()->route('admin.dashboard', ['tab' => 'categorias'])->with('success', 'Categoría creada.');
    }

    public function editarCategoria(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'nombre_categoria' => "required|min:3|regex:/^[A-Za-záéíóúÁÉÍÓÚñÑüÜ\s\(\)\-]+$/u|unique:categoria,nombre,$id,idCategoria",
        ], ['nombre_categoria.unique' => 'Ya existe una categoría con ese nombre.']);
        Categoria::findOrFail($id)->update(['nombre' => $request->nombre_categoria]);
        return redirect()->route('admin.dashboard', ['tab' => 'categorias'])->with('success', 'Categoría actualizada.');
    }

    public function eliminarCategoria(int $id): RedirectResponse
    {
        Categoria::findOrFail($id)->delete();
        return redirect()->route('admin.dashboard', ['tab' => 'categorias'])->with('success', 'Categoría eliminada.');
    }

    // ── EVENTOS ───────────────────────────────────────────────────────────────

    public function crearEvento(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre_evento'  => 'required|min:3',
            'estado_evento'  => 'required|in:activo,inactivo',
            'fecha_entrega'  => 'required|date',
            'lugar_entrega'  => 'required|min:3',
            'titulo_pub'     => 'required|min:3',
            'contenido_pub'  => 'required|min:10',
        ]);
        DB::transaction(function () use ($request) {
            $evento = Evento::create(['Nombre' => $request->nombre_evento, 'estado' => $request->estado_evento]);
            ProgramadorEventos::create(['idEvento' => $evento->idEvento, 'FechaEntrega' => $request->fecha_entrega, 'Lugar' => $request->lugar_entrega]);
            $img = null;
            if ($request->hasFile('imagen_pub') && $request->file('imagen_pub')->isValid())
                $img = file_get_contents($request->file('imagen_pub')->getRealPath());
            Publicacion::create([
                'titulo' => $request->titulo_pub, 'contenido' => $request->contenido_pub,
                'imagen' => $img, 'fechaPublicacion' => now()->format('Y-m-d'),
                'idUsuario' => $request->session()->get('usuario.idUsuario'), 'idEvento' => $evento->idEvento,
            ]);
        });
        return redirect()->route('admin.dashboard', ['tab' => 'eventos'])->with('success', 'Evento creado correctamente.');
    }

    public function editarEvento(Request $request, int $id): RedirectResponse
    {
        $request->validate(['titulo_pub' => 'required', 'contenido_pub' => 'required', 'nombre_evento' => 'required']);

        DB::transaction(function () use ($request, $id) {
            $evento = Evento::findOrFail($id);
            $evento->update(['Nombre' => $request->nombre_evento, 'estado' => $request->estado_evento ?? $evento->estado]);

            if ($evento->programacion) {
                $evento->programacion->update(['FechaEntrega' => $request->fecha_entrega, 'Lugar' => $request->lugar_entrega]);
            }

            if ($pub = $evento->publicacion) {
                $data = ['titulo' => $request->titulo_pub, 'contenido' => $request->contenido_pub];
                if ($request->hasFile('imagen_pub') && $request->file('imagen_pub')->isValid())
                    $data['imagen'] = file_get_contents($request->file('imagen_pub')->getRealPath());
                $pub->update($data);
            }
        });
        return redirect()->route('admin.dashboard', ['tab' => 'eventos'])->with('success', 'Evento actualizado.');
    }

    public function eliminarEvento(int $id): RedirectResponse
    {
        DB::transaction(function () use ($id) {
            $evento = Evento::findOrFail($id);
            $evento->publicaciones()->delete();
            $evento->programacion()->delete();
            $evento->delete();
        });
        return redirect()->route('admin.dashboard', ['tab' => 'eventos'])->with('success', 'Evento eliminado.');
    }

    public function toggleEvento(int $id): RedirectResponse
    {
        $evento = Evento::findOrFail($id);
        $nuevo  = $evento->estado === 'activo' ? 'inactivo' : 'activo';
        $evento->update(['estado' => $nuevo]);
        return redirect()->route('admin.dashboard', ['tab' => 'eventos'])->with('success', "Evento ahora $nuevo.");
    }

    // ── DONACIONES / SOLICITUDES ──────────────────────────────────────────────

    public function cambiarEstadoDonacion(Request $request, int $id): RedirectResponse
    {
        $request->validate(['estado' => 'required|in:pendiente,aprobada,rechazada', 'observacion' => 'nullable|max:250']);
        $donacion = Donacion::with('donantes')->findOrFail($id);
        $donacion->update(['estado' => $request->estado, 'observacion' => $request->observacion]);
        if ($donante = $donacion->donantes->first()) {
            try { Mail::to($donante->email)->send(new NotificacionEstado($donante->nombre, 'donación', $request->estado, $request->observacion ?? '')); } catch (\Exception) {}
        }
        return redirect()->route('admin.dashboard', ['tab' => 'donapp'])->with('success', 'Estado actualizado.');
    }

    public function cambiarEstadoSolicitud(Request $request, int $id): RedirectResponse
    {
        $request->validate(['estado' => 'required|in:pendiente,aprobada,rechazada', 'observacion' => 'nullable|max:250']);
        $solicitud = Solicitud::with('solicitante')->findOrFail($id);
        $solicitud->update([
            'estado'      => $request->estado,
            'observacion' => $request->observacion,
            'idGestor'    => $request->session()->get('usuario.idUsuario'),
        ]);
        if ($sol = $solicitud->solicitante) {
            try { Mail::to($sol->email)->send(new NotificacionEstado($sol->nombre, 'solicitud', $request->estado, $request->observacion ?? '')); } catch (\Exception) {}
        }
        return redirect()->route('admin.dashboard', ['tab' => 'donapp'])->with('success', 'Estado actualizado.');
    }
}
