<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AsisController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PublicController;

// ── PÚBLICAS ─────────────────────────────────────────────────────────────────
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/terminos', [PublicController::class, 'terminos'])->name('terminos');

Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');
Route::get('/registro', [AuthController::class, 'showRegistro'])->name('registro');
Route::post('/registro',[AuthController::class, 'registro'])->name('registro.post');

Route::get('/recuperar',          [AuthController::class, 'showRecuperar'])->name('recuperar');
Route::post('/recuperar',         [AuthController::class, 'enviarRecuperar'])->name('recuperar.post');
Route::get('/restablecer/{token}',[AuthController::class, 'showRestablecer'])->name('restablecer');
Route::post('/restablecer',       [AuthController::class, 'restablecer'])->name('restablecer.post');

// ── ADMINISTRADOR ─────────────────────────────────────────────────────────────
Route::middleware(['auth.role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::post('/usuarios',              [AdminController::class, 'crearUsuario'])->name('usuarios.crear');
    Route::put('/usuarios/{id}',          [AdminController::class, 'editarUsuario'])->name('usuarios.editar');
    Route::patch('/usuarios/{id}/estado', [AdminController::class, 'toggleEstado'])->name('usuarios.estado');

    // Perfil propio del admin (solo campos no sensibles)
    Route::put('/perfil', [AdminController::class, 'actualizarPerfil'])->name('perfil.update');

    // Flujo de corrección de datos sensibles (tipo SofiaPlus)
    Route::post('/perfil/solicitar-correccion', [AdminController::class, 'solicitarCorreccionPerfil'])->name('perfil.solicitarCorreccion');
    Route::post('/usuarios/{id}/solicitar-correccion', [AdminController::class, 'solicitarCorreccionUsuario'])->name('usuarios.solicitarCorreccion');
    Route::patch('/correcciones/{id}/aprobar',  [AdminController::class, 'aprobarCorreccion'])->name('correcciones.aprobar');
    Route::patch('/correcciones/{id}/rechazar', [AdminController::class, 'rechazarCorreccion'])->name('correcciones.rechazar');
    // Visualización segura del soporte (documento de identidad) adjunto a una corrección.
    // GET intencional (no descarga forzada): se muestra inline y queda auditado en verSoporte().
    Route::get('/correcciones/{id}/soporte', [AdminController::class, 'verSoporte'])->name('correcciones.soporte');

    Route::post('/categorias',       [AdminController::class, 'crearCategoria'])->name('categorias.crear');
    Route::put('/categorias/{id}',   [AdminController::class, 'editarCategoria'])->name('categorias.editar');
    Route::delete('/categorias/{id}',[AdminController::class, 'eliminarCategoria'])->name('categorias.eliminar');

    Route::post('/eventos',              [AdminController::class, 'crearEvento'])->name('eventos.crear');
    Route::put('/eventos/{id}',          [AdminController::class, 'editarEvento'])->name('eventos.editar');
    Route::delete('/eventos/{id}',       [AdminController::class, 'eliminarEvento'])->name('eventos.eliminar');
    Route::patch('/eventos/{id}/estado', [AdminController::class, 'toggleEvento'])->name('eventos.estado');

    Route::patch('/donaciones/{id}/estado',  [AdminController::class, 'cambiarEstadoDonacion'])->name('donaciones.estado');
    Route::patch('/solicitudes/{id}/estado', [AdminController::class, 'cambiarEstadoSolicitud'])->name('solicitudes.estado');
});

// ── ASISTENTE ─────────────────────────────────────────────────────────────────
Route::middleware(['auth.role:asistente'])->prefix('asis')->name('asis.')->group(function () {
    Route::get('/dashboard', [AsisController::class, 'dashboard'])->name('dashboard');

    Route::post('/categorias',     [AsisController::class, 'crearCategoria'])->name('categorias.crear');
    Route::put('/categorias/{id}', [AsisController::class, 'editarCategoria'])->name('categorias.editar');

    Route::post('/eventos',              [AsisController::class, 'crearEvento'])->name('eventos.crear');
    Route::put('/eventos/{id}',          [AsisController::class, 'editarEvento'])->name('eventos.editar');
    Route::patch('/eventos/{id}/estado', [AsisController::class, 'toggleEvento'])->name('eventos.estado');

    Route::patch('/donaciones/{id}/estado',  [AsisController::class, 'cambiarEstadoDonacion'])->name('donaciones.estado');
    Route::patch('/solicitudes/{id}/estado', [AsisController::class, 'cambiarEstadoSolicitud'])->name('solicitudes.estado');

    Route::post('/clientes',              [AsisController::class, 'crearCliente'])->name('clientes.crear');
    Route::put('/clientes/{id}',          [AsisController::class, 'editarCliente'])->name('clientes.editar');
    Route::patch('/clientes/{id}/estado', [AsisController::class, 'toggleEstadoCliente'])->name('clientes.estado');
    // Falta esta ruta: el modal "Solicitar corrección" del dashboard (formCorreccionCliente)
    // y ROUTES_ASIS.solicitarCorreccionCliente() en el blade apuntan aquí.
    Route::post('/clientes/{id}/solicitar-correccion', [AsisController::class, 'solicitarCorreccionCliente'])->name('clientes.solicitarCorreccion');

    // FIX: este grupo ya tiene name('asis.'), así que nombrarla 'asis.clientes.historial'
    // generaba la ruta real 'asis.asis.clientes.historial' (doble prefijo).
    Route::get('/clientes/{id}/historial', [AsisController::class, 'historialCliente'])->name('clientes.historial');

    // Perfil propio del asistente (solo campos no sensibles) + solicitud de corrección
    Route::put('/perfil', [AsisController::class, 'actualizarPerfil'])->name('perfil.update');
    Route::post('/perfil/solicitar-correccion', [AsisController::class, 'solicitarCorreccionPerfil'])->name('perfil.solicitarCorreccion');
});

// ── DONANTE / SOLICITANTE ──────────────────────────────────────────────────────
Route::middleware(['auth.role:donante'])->prefix('usuario')->name('usuario.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    Route::post('/donaciones',        [UserController::class, 'crearDonacion'])->name('donaciones.crear');
    Route::put('/donaciones/{id}',    [UserController::class, 'editarDonacion'])->name('donaciones.editar');
    Route::delete('/donaciones/{id}', [UserController::class, 'cancelarDonacion'])->name('donaciones.cancelar');

    Route::post('/solicitudes',       [UserController::class, 'crearSolicitud'])->name('solicitudes.crear');
    Route::put('/solicitudes/{id}',   [UserController::class, 'editarSolicitud'])->name('solicitudes.editar');
    Route::delete('/solicitudes/{id}',[UserController::class, 'cancelarSolicitud'])->name('solicitudes.cancelar');

    // Perfil propio del donante/solicitante (solo campos no sensibles) + solicitud de corrección
    Route::put('/perfil', [UserController::class, 'actualizarPerfil'])->name('perfil.update');
    Route::post('/perfil/solicitar-correccion', [UserController::class, 'solicitarCorreccionPerfil'])->name('perfil.solicitarCorreccion');
});