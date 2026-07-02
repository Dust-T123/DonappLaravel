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

class AsisController extends Controller
{
    public function dashboard(Request $request)
    {
        $idAsis = $request->session()->get('usuario.idUsuario');

        $clientes = Usuario::where('rol', 'donante')
            ->when($request->cli_search, fn($q, $s) =>
                $q->where('nombre', 'like', "%$s%")->orWhere('email', 'like', "%$s%")
            )
            ->when($request->cli_prioridad, fn($q, $p) => $q->where('prioridad', $p))
            ->orderByDesc('idUsuario')->get();

        $categorias = Categoria::with('creadaPor')
            ->when($request->cat_search, fn($q, $s) => $q->where('nombre', 'like', "%$s%"))
            ->orderBy('nombre')->get();

        $donaciones = Donacion::with(['categoria', 'donantes'])
            ->when($request->don_search, fn($q, $s) =>
                $q->where('descripcion', 'like', "%$s%")
                  ->orWhereHas('donantes', fn($q2) => $q2->where('nombre', 'like', "%$s%"))
            )
            ->when($request->don_estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->don_cat, fn($q, $c) => $c ? $q->where('idCategoria', $c) : $q)
            ->orderByDesc('idDonacion')->get();

        $solicitudes = Solicitud::with(['categoria', 'solicitante', 'gestor'])
            ->when($request->sol_search, fn($q, $s) =>
                $q->where('descripcion', 'like', "%$s%")
                  ->orWhereHas('solicitante', fn($q2) => $q2->where('nombre', 'like', "%$s%"))
            )
            ->when($request->sol_estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->sol_cat, fn($q, $c) => $c ? $q->where('idCategoria', $c) : $q)
            ->orderByDesc('idSolicitud')->get();

        $eventos = Evento::with(['publicacion.autor', 'programacion'])
            ->when($request->ev_search, fn($q, $s) => $q->where('Nombre', 'like', "%$s%"))
            ->when($request->ev_estado, fn($q, $e) => $q->where('estado', $e))
            ->orderByDesc('idEvento')->get();
            

        // Stats
        $totalPendientes  = Donacion::where('estado', 'pendiente')->count()
                          + Solicitud::where('estado', 'pendiente')->count();
        $totalDonaciones  = Donacion::count();
        $totalSolicitudes = Solicitud::count();
        $totalAprobadas   = Donacion::where('estado', 'aprobada')->count();
        $totalEventos     = Evento::count();
        $totalClientes    = Usuario::where('rol', 'donante')->count();
        $asisActual       = Usuario::findOrFail($idAsis);

        $donacionesRpt = Donacion::with(['categoria', 'donantes'])->orderByDesc('idDonacion')->get()
    ->map(fn($d) => [
        'idDonacion'    => $d->idDonacion,
        'descripcion'   => $d->descripcion,
        'categoria'     => $d->categoria?->nombre ?? '—',
        'stock'         => $d->stock,
        'estado'        => $d->estado,
        'donante'       => $d->donantes->first()?->nombre ?? '—',
        'fechaCreacion' => $d->donantes->first()?->pivot?->FechaCreacion ?? null,
        'observacion'   => $d->observacion ?? '',
    ])->toArray();
        $solicitudesRpt = Solicitud::with(['categoria', 'solicitante'])->orderByDesc('idSolicitud')->get()
    ->map(fn($s) => [
        'idSolicitud'   => $s->idSolicitud,
        'descripcion'   => $s->descripcion,
        'categoria'     => $s->categoria?->nombre ?? '—',
        'estado'        => $s->estado,
        'solicitante'   => $s->solicitante?->nombre ?? '—',
        'fechaCreacion' => $s->fechaCreacion ?? null,
        'observacion'   => $s->observacion ?? '',
    ])->toArray();
    
        return view('asis.dashboard', compact(
            'clientes', 'categorias', 'donaciones', 'solicitudes', 'eventos', 'asisActual',
            'totalPendientes', 'totalDonaciones', 'totalSolicitudes',
            'totalAprobadas', 'totalEventos', 'totalClientes',
            'donacionesRpt', 'solicitudesRpt'
        ));
    }

    // ── CATEGORÍAS ────────────────────────────────────────────────────────────

    public function crearCategoria(Request $request): RedirectResponse
    {
        $request->validate(['nombre_categoria' => 'required|min:3|unique:categoria,nombre'],
            ['nombre_categoria.unique' => 'Ya existe una categoría con ese nombre.']);
        Categoria::create(['nombre' => $request->nombre_categoria, 'idUsuario' => $request->session()->get('usuario.idUsuario')]);
        return redirect()->route('asis.dashboard', ['tab' => 'categorias'])->with('success', 'Categoría creada.');
    }

    public function editarCategoria(Request $request, int $id): RedirectResponse
    {
        $request->validate(["nombre_categoria" => "required|min:3|unique:categoria,nombre,$id,idCategoria"],
            ['nombre_categoria.unique' => 'Ya existe una categoría con ese nombre.']);
        Categoria::findOrFail($id)->update(['nombre' => $request->nombre_categoria]);
        return redirect()->route('asis.dashboard', ['tab' => 'categorias'])->with('success', 'Categoría actualizada.');
    }

    // ── EVENTOS ───────────────────────────────────────────────────────────────

    public function crearEvento(Request $request): RedirectResponse
    {
        $request->validate(['nombre_evento'=>'required|min:3','fecha_entrega'=>'required|date','lugar_entrega'=>'required|min:3','titulo_pub'=>'required|min:3','contenido_pub'=>'required|min:10']);
        DB::transaction(function () use ($request) {
            $evento = Evento::create(['Nombre'=>$request->nombre_evento,'estado'=>$request->estado_evento??'activo']);
            ProgramadorEventos::create(['idEvento'=>$evento->idEvento,'FechaEntrega'=>$request->fecha_entrega,'Lugar'=>$request->lugar_entrega]);
            $img = $request->hasFile('imagen_pub') ? file_get_contents($request->file('imagen_pub')->getRealPath()) : null;
            Publicacion::create(['titulo'=>$request->titulo_pub,'contenido'=>$request->contenido_pub,'imagen'=>$img,'fechaPublicacion'=>now()->format('Y-m-d'),'idUsuario'=>$request->session()->get('usuario.idUsuario'),'idEvento'=>$evento->idEvento]);
        });
        return redirect()->route('asis.dashboard', ['tab' => 'eventos'])->with('success', 'Evento creado.');
    }

    public function editarEvento(Request $request, int $id): RedirectResponse
    {
        $request->validate(['titulo_pub'=>'required','contenido_pub'=>'required','nombre_evento'=>'required']);
        DB::transaction(function () use ($request, $id) {
            $evento = Evento::findOrFail($id);
            $evento->update(['Nombre'=>$request->nombre_evento,'estado'=>$request->estado_evento??$evento->estado]);
            if ($evento->programacion) $evento->programacion->update(['FechaEntrega'=>$request->fecha_entrega,'Lugar'=>$request->lugar_entrega]);
            if ($pub = $evento->publicacion) {
                $data = ['titulo'=>$request->titulo_pub,'contenido'=>$request->contenido_pub];
                if ($request->hasFile('imagen_pub')) $data['imagen'] = file_get_contents($request->file('imagen_pub')->getRealPath());
                $pub->update($data);
            }
        });
        return redirect()->route('asis.dashboard', ['tab' => 'eventos'])->with('success', 'Evento actualizado.');
    }

    public function toggleEvento(int $id): RedirectResponse
    {
        $evento = Evento::findOrFail($id);
        $nuevo  = $evento->estado === 'activo' ? 'inactivo' : 'activo';
        $evento->update(['estado' => $nuevo]);
        return redirect()->route('asis.dashboard', ['tab' => 'eventos'])->with('success', "Evento ahora $nuevo.");
    }

    // ── DONACIONES / SOLICITUDES ──────────────────────────────────────────────

    public function cambiarEstadoDonacion(Request $request, int $id): RedirectResponse
    {
        $request->validate(['estado'=>'required|in:pendiente,aprobada,rechazada','observacion'=>'nullable|max:250']);
        $donacion = Donacion::with('donantes')->findOrFail($id);
        $donacion->update(['estado'=>$request->estado,'observacion'=>$request->observacion]);
        if ($donante = $donacion->donantes->first()) {
            try { Mail::to($donante->email)->send(new NotificacionEstado($donante->nombre,'donación',$request->estado,$request->observacion??'')); } catch (\Exception) {}
        }
        return redirect()->route('asis.dashboard', ['tab' => 'donapp'])->with('success', 'Estado actualizado.');
    }

    public function cambiarEstadoSolicitud(Request $request, int $id): RedirectResponse
    {
        $request->validate(['estado'=>'required|in:pendiente,aprobada,rechazada','observacion'=>'nullable|max:250']);
        $solicitud = Solicitud::with('solicitante')->findOrFail($id);
        $solicitud->update(['estado'=>$request->estado,'observacion'=>$request->observacion,'idGestor'=>$request->session()->get('usuario.idUsuario')]);
        if ($sol = $solicitud->solicitante) {
            try { Mail::to($sol->email)->send(new NotificacionEstado($sol->nombre,'solicitud',$request->estado,$request->observacion??'')); } catch (\Exception) {}
        }
        return redirect()->route('asis.dashboard', ['tab' => 'donapp'])->with('success', 'Estado actualizado.');
    }

    // ── CLIENTES ──────────────────────────────────────────────────────────────

    public function crearCliente(Request $request): RedirectResponse
    {
        $request->validate(['nombre'=>'required|min:3','tipoDocumento'=>'required','numDocumento'=>'required|numeric|unique:usuario','fechaNacimiento'=>'required|date','direccion'=>'required|min:5','email'=>'required|email|unique:usuario','telefono'=>'required|digits:10','password'=>'required|min:6|confirmed']);
        Usuario::create(['nombre'=>$request->nombre,'tipoDocumento'=>$request->tipoDocumento,'numDocumento'=>$request->numDocumento,'fechaNacimiento'=>$request->fechaNacimiento,'direccion'=>$request->direccion,'email'=>$request->email,'contrasena'=>Hash::make($request->password),'telefono'=>$request->telefono,'rol'=>'donante','estado'=>'activo','necesidad'=>$request->necesidad??null,'prioridad'=>$request->prioridad??null,'observacion_visita'=>$request->observacion_visita??null]);
        return redirect()->route('asis.dashboard', ['tab' => 'clientes'])->with('success', 'Cliente creado correctamente.');
    }

    public function editarCliente(Request $request, int $id): RedirectResponse
    {
        $request->validate(['nombre'=>'required|min:3',"email"=>"required|email|unique:usuario,email,$id,idUsuario","numDocumento"=>"required|numeric|unique:usuario,numDocumento,$id,idUsuario",'telefono'=>'required|digits:10']);
        $usuario = Usuario::findOrFail($id);
        $data = $request->only(['nombre','tipoDocumento','numDocumento','fechaNacimiento','direccion','email','telefono','necesidad','prioridad','observacion_visita']);
        if ($request->filled('password')) {
            $request->validate(['password'=>'min:6|confirmed']);
            $data['contrasena'] = Hash::make($request->password);
        }
        $usuario->update($data);
        return redirect()->route('asis.dashboard', ['tab' => 'clientes'])->with('success', 'Cliente actualizado correctamente.');
    }

    // ── PERFIL ────────────────────────────────────────────────────────────────

    public function actualizarPerfil(Request $request): RedirectResponse
    {
        $id = $request->session()->get('usuario.idUsuario');
        $request->validate(['nombre'=>'required|min:3',"email"=>"required|email|unique:usuario,email,$id,idUsuario",'telefono'=>'required|digits:10']);
        $usuario = Usuario::findOrFail($id);
        $data = $request->only(['nombre','tipoDocumento','numDocumento','fechaNacimiento','direccion','email','telefono']);
        if ($request->filled('password')) {
            $request->validate(['password'=>'min:6|confirmed']);
            $data['contrasena'] = Hash::make($request->password);
        }
        $usuario->update($data);
        $request->session()->put('usuario.nombre', $request->nombre);
        return redirect()->route('asis.dashboard', ['tab' => 'perfil'])->with('success', 'Perfil actualizado.');
    }
    public function toggleEstadoCliente(Request $request, int $id): RedirectResponse
{
    $cliente = Usuario::findOrFail($id);
    $nuevo   = $cliente->estado === 'activo' ? 'inactivo' : 'activo';
    $cliente->update(['estado' => $nuevo]);
    return redirect()->route('asis.dashboard', ['tab' => 'clientes'])
        ->with('success', "Cliente $nuevo correctamente.");
}
// ── HISTORIAL DE CLIENTE ──────────────────────────────────────────────────

public function historialCliente(int $id)
{
    $donaciones = Donacion::with('categoria')
        ->whereHas('donantes', fn($q) => $q->where('usuario.idUsuario', $id))
        ->orderByDesc('idDonacion')
        ->get()
        ->map(function ($d) use ($id) {
            $pivot = $d->donantes()->where('usuario.idUsuario', $id)->first()?->pivot;
            return [
                'idDonacion'  => $d->idDonacion,
                'descripcion' => $d->descripcion,
                'categoria'   => $d->categoria?->nombre ?? '—',
                'estado'      => $d->estado,
                'fecha'       => $pivot?->FechaCreacion
                                    ? \Carbon\Carbon::parse($pivot->FechaCreacion)->format('d/m/Y')
                                    : '—',
            ];
        });

    $solicitudes = Solicitud::with('categoria')
        ->where('idSolicitante', $id)   // ← corregido
        ->orderByDesc('idSolicitud')
        ->get()
        ->map(fn($s) => [
            'idSolicitud' => $s->idSolicitud,
            'descripcion' => $s->descripcion,
            'categoria'   => $s->categoria?->nombre ?? '—',
            'estado'      => $s->estado,
            'fecha'       => $s->fechaCreacion
                                ? \Carbon\Carbon::parse($s->fechaCreacion)->format('d/m/Y')
                                : '—',
        ]);

    return response()->json(compact('donaciones', 'solicitudes'));
}
}
