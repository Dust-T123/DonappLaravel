<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Donacion;
use App\Models\Solicitud;
use App\Models\Categoria;
use App\Models\Evento;
use App\Models\CorreccionDatos;
use App\Models\VisitaDomiciliaria;
use App\Mail\NotificacionEstado;
use App\Mail\NotificacionCorreccion;
use App\Mail\CodigoCambioPassword;
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
                $q->where(fn($q2) => $q2->where('nombre', 'like', "%$s%")->orWhere('apellido', 'like', "%$s%")->orWhere('email', 'like', "%$s%"))
            )
            ->when($request->rol, fn($q, $r) => $q->where('rol', $r))
            ->when($request->prioridad, fn($q, $p) => $q->where('prioridad', $p))
            ->when($request->sort === 'az', fn($q) => $q->orderBy('nombre'))
            ->when($request->sort === 'za', fn($q) => $q->orderByDesc('nombre'))
            ->when($request->sort === 'prioridad', fn($q) => $q->ordenarPorPrioridad())
            ->when(!$request->sort, fn($q) => $q->orderByDesc('idUsuario'))
            ->get();

        // ── Categorías ────────────────────────────────────────────────────────
        $categorias = Categoria::with('creadaPor')
            ->withCount(['donaciones', 'solicitudes'])
            ->when($request->cat_search, fn($q, $s) => $q->where('nombre', 'like', "%$s%"))
            ->when($request->cat_sort === 'za', fn($q) => $q->orderByDesc('nombre'), fn($q) => $q->orderBy('nombre'))
            ->get();

        // ── Donaciones ────────────────────────────────────────────────────────
        $donaciones = Donacion::with(['categoria', 'donantes'])
            ->when($request->don_search, fn($q, $s) =>
                $q->where(fn($q2) => $q2->where('descripcion', 'like', "%$s%")
                  ->orWhereHas('donantes', fn($q3) => $q3->where('nombre', 'like', "%$s%")))
            )
            ->when($request->don_estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->don_cat, fn($q, $c) => $c ? $q->where('idCategoria', $c) : $q)
            ->when($request->don_sort === 'az', fn($q) => $q->orderBy('descripcion'))
            ->when($request->don_sort === 'za', fn($q) => $q->orderByDesc('descripcion'))
            ->when(!$request->don_sort, fn($q) => $q->orderByDesc('idDonacion'))
            ->get();

        // ── Inventario disponible (donaciones APROBADAS agrupadas por artículo) ──
        // Suma el stock de todas las donaciones aprobadas que comparten la misma
        // descripción y categoría, para responder "¿cuánto tenemos de X?"
        // en vez de mostrar cada donación por separado.
        $inventario = Donacion::where('estado', 'aprobada')
            ->when($request->inv_search, fn($q, $s) => $q->where('descripcion', 'like', "%$s%"))
            ->when($request->inv_cat, fn($q, $c) => $c ? $q->where('idCategoria', $c) : $q)
            ->select('descripcion', 'idCategoria', DB::raw('SUM(stock) as total_stock'), DB::raw('COUNT(*) as num_donaciones'))
            ->groupBy('descripcion', 'idCategoria')
            ->with('categoria')
            ->when($request->inv_sort === 'az', fn($q) => $q->orderBy('descripcion'))
            ->when($request->inv_sort === 'za', fn($q) => $q->orderByDesc('descripcion'))
            ->when(!$request->inv_sort, fn($q) => $q->orderByDesc('total_stock'))
            ->get();
        $totalUnidadesInventario = $inventario->sum('total_stock');

        // ── Solicitudes ───────────────────────────────────────────────────────
        $solicitudes = Solicitud::with(['categoria', 'solicitante', 'gestor'])
            ->when($request->sol_search, fn($q, $s) =>
                $q->where(fn($q2) => $q2->where('descripcion', 'like', "%$s%")
                  ->orWhereHas('solicitante', fn($q3) => $q3->where('nombre', 'like', "%$s%")))
            )
            ->when($request->sol_estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->sol_cat, fn($q, $c) => $c ? $q->where('idCategoria', $c) : $q)
            ->when($request->sol_sort === 'az', fn($q) => $q->orderBy('descripcion'))
            ->when($request->sol_sort === 'prioridad', fn($q) =>
                $q->join('usuario', 'usuario.idUsuario', '=', 'solicitud.idSolicitante')
                  ->orderByRaw("FIELD(usuario.prioridad,'urgente','alta','media','baja') IS NULL")
                  ->orderByRaw("FIELD(usuario.prioridad,'urgente','alta','media','baja')")
                  ->select('solicitud.*'))
            ->when(!$request->sol_sort, fn($q) => $q->orderByDesc('idSolicitud'))
            ->get();

        // ── Eventos ───────────────────────────────────────────────────────────
        $eventos = Evento::query()
            ->when($request->ev_search, fn($q, $s) => $q->where('Nombre', 'like', "%$s%"))
            ->when($request->ev_estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->ev_sort === 'az', fn($q) => $q->orderBy('Nombre'))
            ->when($request->ev_sort === 'za', fn($q) => $q->orderByDesc('Nombre'))
            ->when(!$request->ev_sort, fn($q) => $q->orderByDesc('idEvento'))
            ->get();

        // ── Correcciones de datos sensibles pendientes ───────────────────────
        $correcciones = CorreccionDatos::with(['usuario', 'solicitante'])
            ->where('estado', 'pendiente')
            ->orderByDesc('idCorreccion')
            ->get();

        // ── Visitas domiciliarias ─────────────────────────────────────────────
        $visitas = VisitaDomiciliaria::with(['usuario', 'gestor'])
            ->when($request->vis_estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->vis_sort === 'az', fn($q) => $q->orderBy('idUsuario'))
            ->when(!$request->vis_sort, fn($q) => $q->orderByRaw("estado = 'pendiente' DESC")->orderByDesc('idVisita'))
            ->get();
        $totalVisitasPendientes = VisitaDomiciliaria::where('estado', 'pendiente')->count();

        // ── Estadísticas del dashboard ────────────────────────────────────────
        $totalUsuarios   = Usuario::count();
        $totalDonaciones = Donacion::count();
        $totalSolicitudes= Solicitud::count();
        $totalEventos    = Evento::count();
        $totalAprobadas  = Donacion::where('estado', 'aprobada')->count();
        $totalCategorias = Categoria::count();
        $adminActual     = Usuario::findOrFail($idAdmin);

        // ── Datos para reportes PDF (JSON) ────────────────────────────────────
        // OJO: la llave 'fechaCreacion' debe coincidir EXACTAMENTE con lo que
        // lee admin_dashboard.js al filtrar por fecha. Antes se llamaba 'fecha'
        // (donaciones) o no existía (solicitudes), por eso el filtro de fechas
        // de los reportes nunca funcionaba: el campo llegaba como undefined.
        $donacionesRpt = Donacion::with(['categoria', 'donantes'])
            ->orderByDesc('idDonacion')->get()
            ->map(fn($d) => [
                'id'            => $d->idDonacion,
                'descripcion'   => $d->descripcion,
                'categoria'     => $d->categoria?->nombre ?? '—',
                'stock'         => $d->stock,
                'estado'        => $d->estado,
                'fechaCreacion' => $d->donantes->first()?->pivot->FechaCreacion ?? $d->fechaCreacion ?? '',
                'donante'       => $d->donantes->first()?->nombre ?? '—',
                'observacion'   => $d->observacion ?? '',
            ])->toArray();

        $solicitudesRpt = Solicitud::with(['categoria', 'solicitante'])
            ->orderByDesc('idSolicitud')->get()
            ->map(fn($s) => [
                'id'            => $s->idSolicitud,
                'descripcion'   => $s->descripcion,
                'categoria'     => $s->categoria?->nombre ?? '—',
                'estado'        => $s->estado,
                'fechaCreacion' => $s->fechaCreacion ?? '',
                'solicitante'   => $s->solicitante?->nombre ?? '—',
                'observacion'   => $s->observacion ?? '',
            ])->toArray();

        return view('admin.dashboard', compact(
            'usuarios', 'categorias', 'donaciones', 'solicitudes', 'eventos', 'correcciones',
            'totalUsuarios', 'totalDonaciones', 'totalSolicitudes', 'totalEventos',
            'totalAprobadas', 'totalCategorias', 'adminActual',
            'donacionesRpt', 'solicitudesRpt', 'visitas', 'totalVisitasPendientes',
            'inventario', 'totalUnidadesInventario'
        ));
    }

    // ── USUARIOS ──────────────────────────────────────────────────────────────

    public function crearUsuario(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'         => 'required|min:3|max:100',
            'apellido'       => 'required|min:2|max:100',
            'tipoDocumento'  => 'required|in:CC,TI,CE,TE,PPT,Pasaporte',
            'numDocumento'   => 'required|regex:/^[A-Za-z0-9]{4,15}$/|unique:usuario',
            'fechaNacimiento'=> 'required|date|before_or_equal:-14 years',
            'direccion'      => 'required|min:5',
            'email'          => 'required|email|unique:usuario',
            'telefono'       => 'required|digits:10',
            'rol'            => 'required|in:administrador,asistente,donante',
            'estado'         => 'required|in:activo,inactivo',
            'password'       => 'required|min:6|confirmed',
        ], [
            'fechaNacimiento.before_or_equal' => 'El usuario debe tener al menos 14 años.',
        ]);

        $rol = $request->rol;
        Usuario::create([
            'nombre'             => $request->nombre,
            'apellido'           => $request->apellido,
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
            'direccion'      => 'required|min:5',
            'email'          => "required|email|unique:usuario,email,$id,idUsuario",
            'telefono'       => 'required|digits:10',
            'rol'            => 'required|in:administrador,asistente,donante',
            'estado'         => 'required|in:activo,inactivo',
        ];
        if ($request->filled('password')) $rules['password'] = 'min:6|confirmed';
        $request->validate($rules);

        // NOTA: esta pantalla la usa el administrador para editar a OTROS usuarios.
        // nombre/tipoDocumento/numDocumento/fechaNacimiento NO se reciben aquí
        // (los inputs están disabled en la vista): para corregirlos existe el
        // flujo de solicitarCorreccionUsuario() + aprobarCorreccion().
        if ($id === $sesionId) {
            if ($request->rol !== $request->session()->get('usuario.rol'))
                return back()->withErrors(['msg' => 'No puedes cambiar tu propio rol.']);
            if ($request->estado !== 'activo')
                return back()->withErrors(['msg' => 'No puedes desactivar tu propia cuenta.']);
        }

        $usuario = Usuario::findOrFail($id);
        $rol     = $request->rol;
        $data = [
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

    /**
     * Actualiza SOLO los campos no sensibles del propio perfil del admin.
     * nombre / tipoDocumento / numDocumento / fechaNacimiento ya NO se
     * reciben desde este formulario (los inputs están disabled en la vista
     * y no se envían), así que tampoco se validan ni se tocan aquí.
     * Para corregirlos existe el flujo de solicitarCorreccionPerfil().
     */
    public function actualizarPerfil(Request $request): RedirectResponse
    {
        $id = $request->session()->get('usuario.idUsuario');
        $request->validate([
            'direccion' => 'required|min:5|max:255',
            'email'     => "required|email|unique:usuario,email,$id,idUsuario",
            'telefono'  => 'required|digits:10',
        ]);

        $usuario = Usuario::findOrFail($id);
        $data    = $request->only(['direccion', 'email', 'telefono']);
        $usuario->update($data);

        return redirect()->route('admin.dashboard', ['tab' => 'perfil'])->with('success', 'Perfil actualizado.');
    }

    /**
     * PASO 1 — Envía un código de 6 dígitos al correo del propio admin/asistente
     * para poder confirmar un cambio de contraseña. Evita que alguien con la
     * sesión abierta (pero sin acceso a ese correo) pueda tomar la cuenta.
     */
    public function solicitarCodigoPassword(Request $request): RedirectResponse
    {
        $id = $request->session()->get('usuario.idUsuario');
        $usuario = Usuario::findOrFail($id);

        $codigo    = (string) random_int(100000, 999999);
        $codigoHash = hash('sha256', $codigo);

        $usuario->update([
            'reset_token'        => $codigoHash,
            'reset_token_expira' => now()->addMinutes(10),
        ]);

        try {
            Mail::to($usuario->email)->send(new CodigoCambioPassword($usuario->nombre, $codigo));
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo enviar el código. Verifica la configuración de correo.');
        }

        return redirect()->route('admin.dashboard', ['tab' => 'perfil'])
            ->with('success', 'Te enviamos un código a tu correo. Ingrésalo junto con tu nueva contraseña para confirmar el cambio.');
    }

    /**
     * PASO 2 — Verifica el código y, si es válido y no ha expirado (10 min),
     * aplica la nueva contraseña.
     */
    public function confirmarCambioPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'codigo'   => 'required|digits:6',
            'password' => 'required|min:6|confirmed',
        ]);

        $id = $request->session()->get('usuario.idUsuario');
        $usuario = Usuario::findOrFail($id);

        $codigoHash = hash('sha256', $request->codigo);
        $valido = $usuario->reset_token === $codigoHash
            && $usuario->reset_token_expira
            && now()->lt($usuario->reset_token_expira);

        if (!$valido) {
            return back()->with('error', 'El código es incorrecto o ya expiró. Solicita uno nuevo.');
        }

        $usuario->update([
            'contrasena'         => Hash::make($request->password),
            'reset_token'        => null,
            'reset_token_expira' => null,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'perfil'])->with('success', 'Contraseña actualizada correctamente.');
    }

    /**
     * Crea una solicitud de corrección para un campo sensible propio.
     * No modifica el usuario: queda 'pendiente' hasta que OTRO administrador
     * la apruebe desde el tab de Usuarios (ver aprobarCorreccion/rechazarCorreccion).
     */
    public function solicitarCorreccionPerfil(Request $request): RedirectResponse
    {
        $request->validate([
            'campo'          => 'required|in:nombre,apellido,tipoDocumento,numDocumento,fechaNacimiento',
            'valorNuevo'     => 'required|max:150',
            'justificacion'  => 'required|min:10|max:300',
            'soporte'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'consentimiento' => 'required|accepted',
        ]);

        $idUsuario = $request->session()->get('usuario.idUsuario');
        $usuario   = Usuario::findOrFail($idUsuario);
        $soporte   = $this->guardarSoporte($request);

        try {
            CorreccionDatos::create([
                'idUsuario'      => $idUsuario,
                'idSolicitante'  => $idUsuario, // solicita sobre sus propios datos
                'campo'          => $request->campo,
                'valorAnterior'  => (string) $usuario->{$request->campo},
                'valorNuevo'     => $request->valorNuevo,
                'justificacion'  => $request->justificacion,
                'estado'         => 'pendiente',
                'soporteRuta'    => $soporte['ruta'],
                'soporteMime'    => $soporte['mime'],
                'soporteHash'    => $soporte['hash'],
                'consentimiento' => true,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($soporte['ruta']); // evita huérfanos si el trigger rechaza el insert
            // Captura el mensaje del trigger tg_evitar_correccion_duplicada
            return back()->with('error', 'Ya tienes una solicitud pendiente para ese campo. Espera su resolución.');
        }

        return redirect()->route('admin.dashboard', ['tab' => 'perfil'])->with('correccion_ok', true);
    }

    /**
     * Un administrador solicita corregir un campo sensible de OTRO usuario
     * (donante, asistente o incluso otro admin). No aplica el cambio: solo
     * queda registrado como pendiente. Quien la solicita NO podrá aprobarla
     * (ver aprobarCorreccion), aunque los datos no sean los suyos.
     */
    public function solicitarCorreccionUsuario(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'campo'          => 'required|in:nombre,apellido,tipoDocumento,numDocumento,fechaNacimiento',
            'valorNuevo'     => 'required|max:150',
            'justificacion'  => 'required|min:10|max:300',
            'soporte'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'consentimiento' => 'required|accepted',
        ]);

        $idSolicitante = $request->session()->get('usuario.idUsuario');
        $usuarioObjetivo = Usuario::findOrFail($id);
        $soporte = $this->guardarSoporte($request);

        try {
            CorreccionDatos::create([
                'idUsuario'      => $usuarioObjetivo->idUsuario,
                'idSolicitante'  => $idSolicitante,
                'campo'          => $request->campo,
                'valorAnterior'  => (string) $usuarioObjetivo->{$request->campo},
                'valorNuevo'     => $request->valorNuevo,
                'justificacion'  => $request->justificacion,
                'estado'         => 'pendiente',
                'soporteRuta'    => $soporte['ruta'],
                'soporteMime'    => $soporte['mime'],
                'soporteHash'    => $soporte['hash'],
                'consentimiento' => true,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($soporte['ruta']);
            return back()->with('error', 'Ese usuario ya tiene una solicitud pendiente para ese campo. Espera su resolución.');
        }

        return redirect()->route('admin.dashboard', ['tab' => 'usuarios'])
            ->with('success', "Solicitud de corrección enviada para {$usuarioObjetivo->nombre}. Quedará pendiente de aprobación por otro administrador.");
    }

    /**
     * Aprueba una solicitud de corrección y aplica el cambio real al usuario.
     * Nunca puede aprobarla quien la solicitó (doble control), sin importar
     * si los datos corregidos son suyos o de otra persona.
     */
    public function aprobarCorreccion(Request $request, int $id): RedirectResponse
    {
        $idAprobador = $request->session()->get('usuario.idUsuario');
        $correccion  = CorreccionDatos::findOrFail($id);

        if ($correccion->estado !== 'pendiente') {
            return back()->with('error', 'Esta solicitud ya fue resuelta anteriormente.');
        }
        if ($correccion->idSolicitante === $idAprobador) {
            return back()->with('error', 'No puedes aprobar una solicitud de corrección que tú mismo pediste. Debe hacerlo otro administrador.');
        }

        $valor = trim($correccion->valorNuevo);

        switch ($correccion->campo) {
            case 'numDocumento':
                if (!preg_match('/^[A-Za-z0-9]{4,15}$/', $valor)) {
                    return back()->with('error', 'El número de documento propuesto no es válido.');
                }
                if (Usuario::where('numDocumento', $valor)->where('idUsuario', '!=', $correccion->idUsuario)->exists()) {
                    return back()->with('error', 'Ese número de documento ya está registrado por otro usuario.');
                }
                break;
            case 'tipoDocumento':
                if (!in_array($valor, ['CC', 'TI', 'CE', 'TE', 'PPT', 'PEP'])) {
                    return back()->with('error', 'Tipo de documento propuesto no es válido.');
                }
                break;
            case 'fechaNacimiento':
                if (!strtotime($valor)) {
                    return back()->with('error', 'La fecha de nacimiento propuesta no es válida.');
                }
                if (\Carbon\Carbon::parse($valor)->age < 14) {
                    return back()->with('error', 'La fecha de nacimiento propuesta indica una edad menor a 14 años, no permitida en la plataforma.');
                }
                break;
            case 'nombre':
                if (mb_strlen($valor) < 3) {
                    return back()->with('error', 'El nombre propuesto debe tener al menos 3 caracteres.');
                }
                break;
            case 'apellido':
                if (mb_strlen($valor) < 2) {
                    return back()->with('error', 'El apellido propuesto debe tener al menos 2 caracteres.');
                }
                break;
        }

        $usuarioAfectado = Usuario::findOrFail($correccion->idUsuario);
        $usuarioAfectado->update([$correccion->campo => $valor]);

        $correccion->update([
            'estado'          => 'aprobada',
            'idAprobador'     => $idAprobador,
            'fechaResolucion' => now(),
        ]);

        $this->purgarSoporte($correccion); // el documento de identidad ya cumplió su propósito, no se conserva

        try {
            Mail::to($usuarioAfectado->email)->send(new NotificacionCorreccion(
                $usuarioAfectado->nombre, $correccion->campo, 'aprobada', $valor
            ));
        } catch (\Exception) {}

        return redirect()->route('admin.dashboard', ['tab' => 'usuarios'])->with('success', 'Corrección aprobada y aplicada al usuario.');
    }

    /**
     * Guarda el soporte (foto/PDF del documento) en disco PRIVADO, nunca público
     * ni por correo. Solo se referencia por ruta + hash de integridad en la BD.
     */
    private function guardarSoporte(Request $request): array
    {
        $archivo = $request->file('soporte');
        $ruta    = $archivo->store('correcciones-soportes', 'local');
        return [
            'ruta' => $ruta,
            'mime' => $archivo->getClientMimeType(),
            'hash' => hash_file('sha256', $archivo->getRealPath()),
        ];
    }

    /**
     * Borra físicamente el soporte una vez la corrección fue resuelta
     * (aprobada o rechazada). Principio de minimización/retención limitada.
     */
    private function purgarSoporte(CorreccionDatos $correccion): void
    {
        if ($correccion->soporteRuta) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($correccion->soporteRuta);
            $correccion->update(['soporteRuta' => null, 'soporteMime' => null]);
        }
    }

    /**
     * Muestra el soporte SOLO a un administrador distinto de quien solicitó
     * la corrección, y deja rastro en logs de quién lo consultó y cuándo
     * (accountability / responsabilidad demostrada, Ley 1581).
     */
    public function verSoporte(Request $request, int $id)
    {
        $idAdmin    = $request->session()->get('usuario.idUsuario');
        $correccion = CorreccionDatos::findOrFail($id);

        if ($correccion->idSolicitante === $idAdmin) {
            abort(403, 'No puedes ver el soporte de una solicitud que tú mismo hiciste.');
        }
        if (!$correccion->soporteRuta || !\Illuminate\Support\Facades\Storage::disk('local')->exists($correccion->soporteRuta)) {
            abort(404, 'El soporte ya no está disponible (fue purgado o la solicitud ya se resolvió).');
        }

        \Illuminate\Support\Facades\Log::info('Consulta de soporte de corrección', [
            'idCorreccion' => $id, 'idAdminConsulta' => $idAdmin, 'fecha' => now(),
        ]);

        return \Illuminate\Support\Facades\Storage::disk('local')->response(
            $correccion->soporteRuta,
            null,
            ['Content-Disposition' => 'inline; filename="soporte-correccion-' . $id . '.' . ($correccion->soporteMime === 'application/pdf' ? 'pdf' : 'jpg') . '"']
        );
    }

    public function rechazarCorreccion(Request $request, int $id): RedirectResponse
    {
        $idAprobador = $request->session()->get('usuario.idUsuario');
        $correccion  = CorreccionDatos::findOrFail($id);

        if ($correccion->estado !== 'pendiente') {
            return back()->with('error', 'Esta solicitud ya fue resuelta anteriormente.');
        }

        $request->validate(['motivo' => 'nullable|max:300']);

        $correccion->update([
            'estado'                => 'rechazada',
            'idAprobador'           => $idAprobador,
            'fechaResolucion'       => now(),
            'observacionResolucion' => $request->input('motivo'),
        ]);

        $this->purgarSoporte($correccion);

        $usuarioAfectado = $correccion->usuario;
        if ($usuarioAfectado) {
            try {
                Mail::to($usuarioAfectado->email)->send(new NotificacionCorreccion(
                    $usuarioAfectado->nombre, $correccion->campo, 'rechazada', '', $request->input('motivo', '')
                ));
            } catch (\Exception) {}
        }

        return redirect()->route('admin.dashboard', ['tab' => 'usuarios'])->with('success', 'Corrección rechazada.');
    }

    // ── CATEGORÍAS ────────────────────────────────────────────────────────────

    public function crearCategoria(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre_categoria' => 'required|min:3|max:100|regex:/^[A-Za-záéíóúÁÉÍÓÚñÑüÜ\s\(\)\-]+$/u|unique:categoria,nombre',
        ], [
            'nombre_categoria.unique' => 'Ya existe una categoría con este nombre. Verifica antes de crear una nueva.',
            'nombre_categoria.regex'  => 'Solo se permiten letras, espacios, guiones y paréntesis.',
        ]);

        Categoria::create([
            'nombre'    => trim($request->nombre_categoria),
            'idUsuario' => $request->session()->get('usuario.idUsuario'),
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'categorias'])->with('success', 'Categoría creada.');
    }

    public function editarCategoria(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'nombre_categoria' => "required|min:3|max:100|regex:/^[A-Za-záéíóúÁÉÍÓÚñÑüÜ\s\(\)\-]+$/u|unique:categoria,nombre,$id,idCategoria",
        ], [
            'nombre_categoria.unique' => 'Ya existe una categoría con este nombre. Verifica antes de guardar.',
            'nombre_categoria.regex'  => 'Solo se permiten letras, espacios, guiones y paréntesis.',
        ]);

        Categoria::findOrFail($id)->update(['nombre' => trim($request->nombre_categoria)]);

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
            'nombre_evento'  => 'required|min:3|max:150',
            'estado_evento'  => 'required|in:borrador,publicado,en_curso,finalizado,cancelado',
            'fecha_inicio'   => 'required|date|after_or_equal:today',
            'fecha_fin'      => ['required', 'date', 'after_or_equal:fecha_inicio', 'after_or_equal:today'],
            'lugar_entrega'  => 'required|min:3|max:255',
            'titulo_pub'     => 'required|min:3|max:200',
            'contenido_pub'  => 'required|min:10|max:500',
        ], [
            'fecha_inicio.after_or_equal' => 'No se pueden crear eventos con fechas pasadas. Selecciona hoy o una fecha futura.',
            'fecha_fin.after_or_equal'    => 'La fecha de fin debe ser hoy o una fecha futura, y no puede ser anterior a la fecha de inicio.',
        ]);

        $img = null;
        if ($request->hasFile('imagen_pub') && $request->file('imagen_pub')->isValid())
            $img = file_get_contents($request->file('imagen_pub')->getRealPath());

        Evento::create([
            'Nombre'           => $request->titulo_pub ?: $request->nombre_evento,
            'contenido'        => $request->contenido_pub,
            'imagen'           => $img,
            'fechaPublicacion' => now()->format('Y-m-d'),
            'fechaInicio'      => $request->fecha_inicio,
            'fechaFin'         => $request->fecha_fin,
            'lugar'            => $request->lugar_entrega,
            'estado'           => $request->estado_evento,
        ]);

        return redirect()->route('admin.dashboard', ['tab' => 'eventos'])->with('success', 'Evento creado correctamente.');
    }

    public function editarEvento(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'nombre_evento'  => 'required|min:3|max:150',
            'titulo_pub'     => 'required|min:3|max:200',
            'contenido_pub'  => 'required|min:10|max:500',
            'fecha_inicio'   => 'required|date',
            'fecha_fin'      => 'required|date|after_or_equal:fecha_inicio',
            'lugar_entrega'  => 'required|min:3|max:255',
            'estado_evento'  => 'nullable|in:borrador,publicado,en_curso,finalizado,cancelado',
        ], [
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',
        ]);

        $evento = Evento::findOrFail($id);
        $data = [
            'Nombre'      => $request->titulo_pub ?: $request->nombre_evento,
            'contenido'   => $request->contenido_pub,
            'fechaInicio' => $request->fecha_inicio,
            'fechaFin'    => $request->fecha_fin,
            'lugar'       => $request->lugar_entrega,
            'estado'      => $request->estado_evento ?? $evento->estado,
        ];
        if ($request->hasFile('imagen_pub') && $request->file('imagen_pub')->isValid())
            $data['imagen'] = file_get_contents($request->file('imagen_pub')->getRealPath());
        $evento->update($data);

        return redirect()->route('admin.dashboard', ['tab' => 'eventos'])->with('success', 'Evento actualizado.');
    }

    public function eliminarEvento(int $id): RedirectResponse
    {
        Evento::findOrFail($id)->delete();
        return redirect()->route('admin.dashboard', ['tab' => 'eventos'])->with('success', 'Evento eliminado.');
    }

    public function cambiarEstadoEvento(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'estado' => 'required|in:borrador,publicado,en_curso,finalizado,cancelado',
        ]);
        $evento = Evento::findOrFail($id);
        $evento->update(['estado' => $request->estado]);
        $etiquetas = ['borrador' => 'Borrador', 'publicado' => 'Publicado', 'en_curso' => 'En curso', 'finalizado' => 'Finalizado', 'cancelado' => 'Cancelado'];
        return redirect()->route('admin.dashboard', ['tab' => 'eventos'])->with('success', 'Estado del evento actualizado a: ' . ($etiquetas[$request->estado] ?? $request->estado));
    }

    // ── DONACIONES / SOLICITUDES ──────────────────────────────────────────────

    public function cambiarEstadoDonacion(Request $request, int $id): RedirectResponse
    {
        $request->validate(['estado' => 'required|in:pendiente,aprobada,rechazada,cancelada,completada', 'observacion' => 'nullable|max:250']);
        $donacion = Donacion::with('donantes')->findOrFail($id);
        $donacion->update(['estado' => $request->estado, 'observacion' => $request->observacion]);
        if ($donante = $donacion->donantes->first()) {
            try { Mail::to($donante->email)->send(new NotificacionEstado($donante->nombre, 'donación', $request->estado, $request->observacion ?? '')); } catch (\Exception) {}
        }
        return redirect()->route('admin.dashboard', ['tab' => 'donapp'])->with('success', 'Estado actualizado.');
    }

    public function cambiarEstadoSolicitud(Request $request, int $id): RedirectResponse
    {
        $request->validate(['estado' => 'required|in:pendiente,aprobada,rechazada,cancelada,completada', 'observacion' => 'nullable|max:250']);
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

    public function cambiarEstadoVisita(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'estado'      => 'required|in:aprobada,rechazada,realizada,cancelada',
            'observacion' => 'nullable|max:300',
        ]);

        $visita = VisitaDomiciliaria::with('usuario')->findOrFail($id);
        $visita->update([
            'estado'          => $request->estado,
            'observacion'     => $request->observacion,
            'idGestor'        => $request->session()->get('usuario.idUsuario'),
            'fechaResolucion' => now(),
        ]);

        if ($u = $visita->usuario) {
            try { Mail::to($u->email)->send(new NotificacionEstado($u->nombre, 'visita domiciliaria', $request->estado, $request->observacion ?? '')); } catch (\Exception) {}
        }

        return redirect()->route('admin.dashboard', ['tab' => 'visitas'])->with('success', 'Estado de la visita actualizado.');
    }
}