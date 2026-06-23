<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Mail\RecuperacionContrasena;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ── LOGIN ─────────────────────────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'contrasena'=> 'required',
        ], [
            'email.required'      => 'El correo es obligatorio.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario) {
            return back()->withErrors(['msg' => 'No encontramos una cuenta con ese correo electrónico.']);
        }

        if ($usuario->estado !== 'activo') {
            return back()->withErrors(['msg' => 'Tu acceso ha sido restringido por el administrador.']);
        }

        if (!Hash::check($request->contrasena, $usuario->contrasena)) {
            return back()->withErrors(['msg' => 'Contraseña incorrecta. Verifica tus credenciales.']);
        }

        // Guardar sesión
        $request->session()->put('usuario', [
            'idUsuario'     => $usuario->idUsuario,
            'nombre'        => $usuario->nombre,
            'rol'           => $usuario->rol,
            'password_hash' => $usuario->contrasena,
        ]);

        $ruta = match ($usuario->rol) {
    'administrador' => route('admin.dashboard'),
    'asistente'     => route('asis.dashboard'),
    default         => route('usuario.dashboard'),
};

return response()->view('auth.login_success', [
    'nombre' => $usuario->nombre,
    'ruta'   => $ruta,
]);
    }

    // ── LOGOUT ────────────────────────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->flush();
        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }

    // ── REGISTRO ──────────────────────────────────────────────────────────────

    public function showRegistro()
    {
        return view('auth.registro');
    }

    public function registro(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'       => 'required|min:3|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]+$/',
            'tipoDocumento'=> 'required',
            'numDocumento' => 'required|numeric|digits_between:4,15|unique:usuario',
            'fechaNacimiento'=> 'required|date|before:-5 years',
            'direccion'    => 'required|min:5|max:255',
            'email'        => 'required|email|unique:usuario',
            'telefono'     => 'required|digits:10',
            'password'     => 'required|min:6|confirmed',
        ], [
            'email.unique'       => 'Este correo ya está registrado.',
            'numDocumento.unique'=> 'Este número de documento ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'nombre.regex'       => 'El nombre solo puede contener letras y espacios.',
        ]);

        Usuario::create([
            'nombre'          => $request->nombre,
            'tipoDocumento'   => $request->tipoDocumento,
            'numDocumento'    => $request->numDocumento,
            'fechaNacimiento' => $request->fechaNacimiento,
            'direccion'       => $request->direccion,
            'email'           => $request->email,
            'contrasena'      => Hash::make($request->password),
            'telefono'        => $request->telefono,
            'rol'             => 'donante',
            'estado'          => 'activo',
            'necesidad'       => $request->necesidad ?: null,
        ]);

        return redirect()->route('login')->with('success', '¡Registro exitoso! Ya puedes iniciar sesión.');
    }

    // ── RECUPERACIÓN DE CONTRASEÑA ────────────────────────────────────────────

    public function showRecuperar()
    {
        return view('auth.recuperar');
    }

    public function enviarRecuperar(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        // Siempre responder igual (seguridad anti-enumeración)
        $msg = 'Si el correo pertenece a una cuenta activa, recibirás las instrucciones.';

        $usuario = Usuario::where('email', $request->email)
            ->where('estado', 'activo')
            ->first();

        if ($usuario) {
            $tokenRaw  = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $tokenRaw);
            $expira    = now()->addHour()->format('Y-m-d H:i:s');

            $usuario->update([
                'reset_token'        => $tokenHash,
                'reset_token_expira' => $expira,
            ]);

            $link = route('restablecer', ['token' => $tokenRaw]);
            Mail::to($usuario->email)->send(new RecuperacionContrasena($usuario->nombre, $link));
        }

        return back()->with('success', $msg);
    }

    public function showRestablecer(string $token)
    {
        $tokenHash = hash('sha256', $token);
        $usuario   = Usuario::where('reset_token', $tokenHash)
            ->where('reset_token_expira', '>', now())
            ->where('estado', 'activo')
            ->first();

        if (!$usuario) {
            return redirect()->route('recuperar')->withErrors(['msg' => 'El enlace es inválido o ha expirado.']);
        }

        return view('auth.restablecer', ['token' => $token]);
    }

    public function restablecer(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => 'required',
            'password' => 'required|min:6|confirmed',
        ], ['password.confirmed' => 'Las contraseñas no coinciden.']);

        $tokenHash = hash('sha256', $request->token);
        $usuario   = Usuario::where('reset_token', $tokenHash)
            ->where('reset_token_expira', '>', now())
            ->where('estado', 'activo')
            ->first();

        if (!$usuario) {
            return redirect()->route('recuperar')->withErrors(['msg' => 'El enlace es inválido o ha expirado. Solicita uno nuevo.']);
        }

        $usuario->update([
            'contrasena'         => Hash::make($request->password),
            'reset_token'        => null,
            'reset_token_expira' => null,
        ]);

        return redirect()->route('login')->with('success', 'Contraseña restablecida. Ya puedes iniciar sesión.');
    }
}
