<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Donacion;
use App\Models\Solicitud;
use App\Models\Evento;
use App\Models\Categoria;

/**
 * ApiPublicController
 *
 * Controlador de la API pública de Donapp.
 * No requiere autenticación — datos accesibles por cualquier cliente
 * (app móvil, sitio web externo, Postman, etc.)
 */
class ApiPublicController extends Controller
{
    // ── 1. ESTADÍSTICAS GENERALES ─────────────────────────────────────────
    //
    // GET /api/estadisticas
    //
    // Devuelve los contadores que aparecen en la landing page.
    // Útil para que la app móvil muestre las mismas estadísticas
    // actualizadas en tiempo real sin recargar toda la página.
    //
    public function estadisticas()
    {
        $data = [
            'total_donantes'          => Usuario::where('rol', 'donante')
                                                ->where('estado', 'activo')
                                                ->count(),

            'total_donaciones'        => Donacion::count(),

            'donaciones_aprobadas'    => Donacion::where('estado', 'aprobada')->count(),

            'donaciones_pendientes'   => Donacion::where('estado', 'pendiente')->count(),

            'total_solicitudes'       => Solicitud::count(),

            'solicitudes_aprobadas'   => Solicitud::where('estado', 'aprobada')->count(),

            'eventos_activos'         => Evento::whereIn('estado', ['publicado', 'en_curso'])->count(),

            'total_categorias'        => Categoria::count(),
        ];

        return response()->json([
            'success'   => true,
            'mensaje'   => 'Estadísticas generales de Donapp',
            'data'      => $data,
            'generado'  => now()->format('d/m/Y H:i:s'),
        ]);
    }

    // ── 2. LISTA DE EVENTOS ACTIVOS ───────────────────────────────────────
    //
    // GET /api/eventos
    //
    // Devuelve todos los eventos activos con su publicación y programación.
    // La app móvil puede mostrar esto en una pantalla de "Próximos Eventos".
    //
    public function eventos()
    {
        $eventos = Evento::whereIn('estado', ['publicado', 'en_curso'])
            ->orderByDesc('idEvento')
            ->get()
            ->map(fn($ev) => [
                'id'     => $ev->idEvento,
                'nombre' => $ev->Nombre,
                'estado' => $ev->estado,

                // Fecha y lugar de entrega (antes vivían en ProgramadorEventos,
                // ahora son columnas propias de evento).
                'programacion' => [
                    'fecha_inicio'  => $ev->fechaInicio,
                    'fecha_fin'     => $ev->fechaFin,
                    'lugar'         => $ev->lugar,
                ],

                // Contenido publicado sobre el evento (antes vivía en la tabla
                // publicacion, ahora son columnas propias de evento).
                'publicacion' => [
                    'titulo'            => $ev->Nombre,
                    'contenido'         => $ev->contenido,
                    'fecha_publicacion' => $ev->fechaPublicacion,
                    // La imagen se devuelve en base64 para que la app pueda mostrarla
                    'imagen'            => $ev->imagenBase64(),
                ],
            ]);

        return response()->json([
            'success' => true,
            'total'   => $eventos->count(),
            'data'    => $eventos,
        ]);
    }

    // ── 3. DETALLE DE UN EVENTO ───────────────────────────────────────────
    //
    // GET /api/eventos/{id}
    //
    // Devuelve el detalle completo de un evento específico.
    // Útil cuando el usuario toca una tarjeta de evento en la app.
    //
    public function evento(int $id)
    {
        $ev = Evento::where('idEvento', $id)
            ->whereIn('estado', ['publicado', 'en_curso'])
            ->first();

        if (!$ev) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Evento no encontrado o inactivo',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'     => $ev->idEvento,
                'nombre' => $ev->Nombre,
                'estado' => $ev->estado,

                'programacion' => [
                    'fecha_inicio'  => $ev->fechaInicio,
                    'fecha_fin'     => $ev->fechaFin,
                    'lugar'         => $ev->lugar,
                ],

                'publicacion' => [
                    'titulo'            => $ev->Nombre,
                    'contenido'         => $ev->contenido,
                    'fecha_publicacion' => $ev->fechaPublicacion,
                    'imagen'            => $ev->imagenBase64(),
                ],
            ],
        ]);
    }
}