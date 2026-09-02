<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiPublicController;
// ── ESTADÍSTICAS PÚBLICAS ─────────────────────────────────────────────────
Route::get('/estadisticas', [ApiPublicController::class, 'estadisticas']);

// ── EVENTOS PÚBLICOS ──────────────────────────────────────────────────────
Route::get('/eventos',          [ApiPublicController::class, 'eventos']);
Route::get('/eventos/{id}',     [ApiPublicController::class, 'evento']);