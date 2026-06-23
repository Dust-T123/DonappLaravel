<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: CheckRole
 * Verifica que el usuario esté autenticado en sesión y tenga el rol requerido.
 * Además valida que la cuenta siga activa y que el hash de contraseña no haya cambiado.
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string $rol): Response
    {
        $usuario = $request->session()->get('usuario');

        if (!$usuario) {
            return redirect()->route('login')->withErrors(['msg' => 'Debes iniciar sesión.']);
        }

        // Re-validar en BD para detectar cuentas desactivadas o contraseña cambiada
        $db  = app('db')->connection();
        $row = $db->selectOne(
            'SELECT idUsuario, rol, estado, contrasena FROM usuario WHERE idUsuario = ?',
            [$usuario['idUsuario']]
        );

        if (!$row || $row->estado !== 'activo') {
            $request->session()->flush();
            return redirect()->route('login')->withErrors(['msg' => 'Tu cuenta ha sido desactivada.']);
        }

        if ($row->rol !== $rol) {
            return redirect()->route('login')->withErrors(['msg' => 'Acceso no autorizado.']);
        }

        // Detectar cambio de contraseña externo (sesión invalidada)
        if (isset($usuario['password_hash']) && $usuario['password_hash'] !== $row->contrasena) {
            $request->session()->flush();
            return redirect()->route('login')->withErrors(['msg' => 'Tu sesión ha expirado.']);
        }

        return $next($request);
    }
}
