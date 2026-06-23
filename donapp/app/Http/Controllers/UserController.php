<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Donacion;
use App\Models\Solicitud;
use App\Models\Categoria;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private function idCliente(Request $request): int
    {
        return (int) $request->session()->get('usuario.idUsuario');
    }

    public function dashboard(Request $request)
    {
        $id        = $this->idCliente($request);
        $tabActivo = $request->get('tab', 'inicio');

        $misDonaciones = Donacion::with(['categoria', 'donantes'])
            ->whereHas('donantes', fn($q) => $q->where('idDonante', $id))
            ->with(['donantes' => fn($q) => $q->where('idDonante', $id)])
            ->when($request->don_buscar, fn($q, $s) => $q->where('descripcion', 'like', "%$s%"))
            ->when($request->don_estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->don_cat,    fn($q, $c) => $c ? $q->where('idCategoria', $c) : $q)
            ->orderByDesc('idDonacion')->get();

        $misSolicitudes = Solicitud::with('categoria')
            ->where('idSolicitante', $id)
            ->when($request->sol_buscar, fn($q, $s) => $q->where('descripcion', 'like', "%$s%"))
            ->when($request->sol_estado, fn($q, $e) => $q->where('estado', $e))
            ->when($request->sol_cat,    fn($q, $c) => $c ? $q->where('idCategoria', $c) : $q)
            ->orderByDesc('idSolicitud')->get();

        $categorias = Categoria::orderBy('nombre')->get();

        $eventos = Evento::with(['publicacion', 'programacion'])
            ->where('estado', 'activo')
            ->orderByDesc('idEvento')->get();

        $usuario = Usuario::findOrFail($id);

        // Stats del usuario
        $donBase = Donacion::whereHas('donantes', fn($q) => $q->where('idDonante', $id));
        $stats = [
            'total_don'      => (clone $donBase)->count(),
            'don_pendientes' => (clone $donBase)->where('estado', 'pendiente')->count(),
            'don_aprobadas'  => (clone $donBase)->where('estado', 'aprobada')->count(),
            'total_sol'      => Solicitud::where('idSolicitante', $id)->count(),
            'sol_pendientes' => Solicitud::where('idSolicitante', $id)->where('estado', 'pendiente')->count(),
            'sol_aprobadas'  => Solicitud::where('idSolicitante', $id)->where('estado', 'aprobada')->count(),
        ];

        return view('user.dashboard', compact(
            'misDonaciones', 'misSolicitudes', 'categorias',
            'eventos', 'usuario', 'stats', 'tabActivo'
        ));
    }

    // ── DONACIONES ────────────────────────────────────────────────────────────

    public function crearDonacion(Request $request): RedirectResponse
    {
        $request->validate([
            'descripcion' => 'required|min:5|max:200',
            'idCategoria' => 'required|exists:categoria,idCategoria',
            'stock'       => 'required|integer|min:1|max:9999',
        ]);

        DB::transaction(function () use ($request) {
            $imgData  = $request->hasFile('imagen') && $request->file('imagen')->isValid()
                        ? file_get_contents($request->file('imagen')->getRealPath()) : null;
            $donacion = Donacion::create([
                'descripcion' => $request->descripcion,
                'imagen'      => $imgData,
                'estado'      => 'pendiente',
                'stock'       => $request->stock,
                'idCategoria' => $request->idCategoria,
            ]);
            DB::table('DonacionUsuario')->insert([
                'FechaCreacion' => now()->format('Y-m-d'),
                'idDonante'     => $request->session()->get('usuario.idUsuario'),
                'idDonacion'    => $donacion->idDonacion,
            ]);
        });

        return redirect()->route('usuario.dashboard', ['tab' => 'donaciones'])
            ->with('success', '¡Donación registrada! Queda pendiente de revisión.');
    }

    public function editarDonacion(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'descripcion' => 'required|min:5|max:200',
            'idCategoria' => 'required|exists:categoria,idCategoria',
            'stock'       => 'required|integer|min:1|max:9999',
        ]);

        $idCliente = $this->idCliente($request);
        $donacion  = Donacion::whereHas('donantes', fn($q) => $q->where('idDonante', $idCliente))
            ->where('idDonacion', $id)->where('estado', 'pendiente')->firstOrFail();

        $data = $request->only(['descripcion', 'idCategoria', 'stock']);
        if ($request->hasFile('imagen') && $request->file('imagen')->isValid())
            $data['imagen'] = file_get_contents($request->file('imagen')->getRealPath());
        $donacion->update($data);

        return redirect()->route('usuario.dashboard', ['tab' => 'donaciones'])
            ->with('success', 'Donación actualizada correctamente.');
    }

    public function cancelarDonacion(Request $request, int $id): RedirectResponse
    {
        $idCliente = $this->idCliente($request);
        $donacion  = Donacion::whereHas('donantes', fn($q) => $q->where('idDonante', $idCliente))
            ->where('idDonacion', $id)->where('estado', 'pendiente')->firstOrFail();

        DB::transaction(function () use ($donacion) {
            DB::table('DonacionUsuario')->where('idDonacion', $donacion->idDonacion)->delete();
            $donacion->delete();
        });

        return redirect()->route('usuario.dashboard', ['tab' => 'donaciones'])
            ->with('success', 'Donación cancelada correctamente.');
    }

    // ── SOLICITUDES ───────────────────────────────────────────────────────────

    public function crearSolicitud(Request $request): RedirectResponse
    {
        $request->validate([
            'descripcion' => 'required|min:5|max:300',
            'idCategoria' => 'required|exists:categoria,idCategoria',
        ]);

        $id      = $this->idCliente($request);
        $imgData = $request->hasFile('imagen') && $request->file('imagen')->isValid()
                   ? file_get_contents($request->file('imagen')->getRealPath()) : null;

        Solicitud::create([
            'descripcion'   => $request->descripcion,
            'imagen'        => $imgData,
            'estado'        => 'pendiente',
            'idSolicitante' => $id,
            'idCategoria'   => $request->idCategoria,
            'idGestor'      => $id,
        ]);

        return redirect()->route('usuario.dashboard', ['tab' => 'solicitudes'])
            ->with('success', '¡Solicitud registrada! Queda pendiente de revisión.');
    }

    public function editarSolicitud(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'descripcion' => 'required|min:5|max:300',
            'idCategoria' => 'required|exists:categoria,idCategoria',
        ]);

        $idCliente = $this->idCliente($request);
        $solicitud = Solicitud::where('idSolicitud', $id)
            ->where('idSolicitante', $idCliente)->where('estado', 'pendiente')->firstOrFail();

        $data = $request->only(['descripcion', 'idCategoria']);
        if ($request->hasFile('imagen') && $request->file('imagen')->isValid())
            $data['imagen'] = file_get_contents($request->file('imagen')->getRealPath());
        $solicitud->update($data);

        return redirect()->route('usuario.dashboard', ['tab' => 'solicitudes'])
            ->with('success', 'Solicitud actualizada correctamente.');
    }

    public function cancelarSolicitud(Request $request, int $id): RedirectResponse
    {
        $idCliente = $this->idCliente($request);
        Solicitud::where('idSolicitud', $id)
            ->where('idSolicitante', $idCliente)->where('estado', 'pendiente')
            ->firstOrFail()->delete();

        return redirect()->route('usuario.dashboard', ['tab' => 'solicitudes'])
            ->with('success', 'Solicitud cancelada correctamente.');
    }

    // ── PERFIL ────────────────────────────────────────────────────────────────

    public function actualizarPerfil(Request $request): RedirectResponse
    {
        $id = $this->idCliente($request);
        $request->validate([
            'nombre'       => 'required|min:3|max:100',
            'email'        => "required|email|unique:usuario,email,$id,idUsuario",
            'numDocumento' => "required|numeric|unique:usuario,numDocumento,$id,idUsuario",
            'telefono'     => 'required|digits:10',
        ]);

        $usuario = Usuario::findOrFail($id);

        if ($request->filled('password')) {
            if (!Hash::check($request->password_actual, $usuario->contrasena))
                return back()->withErrors(['msg' => 'La contraseña actual es incorrecta.']);
            $request->validate(['password' => 'min:6|confirmed']);
        }

        $data = $request->only(['nombre','tipoDocumento','numDocumento','fechaNacimiento','direccion','email','telefono']);
        $data['necesidad'] = $request->necesidad ?: null;

        if ($request->filled('password'))
            $data['contrasena'] = Hash::make($request->password);

        $usuario->update($data);
        $request->session()->put('usuario.nombre', $request->nombre);

        return redirect()->route('usuario.dashboard', ['tab' => 'perfil'])
            ->with('success', 'Perfil actualizado correctamente.');
    }
}
