<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Alinea las máquinas de estado de evento/donacion/solicitud con el diagrama
 * de clases (EstadoEvento, EstadoDonacion, EstadoSolicitud), y separa la
 * fecha puntual de entrega de un evento en un rango (fechaInicio/fechaFin),
 * ya que un evento puede durar más de un día.
 *
 * Antes de este cambio, evento solo distinguía 'activo'/'inactivo' y
 * donacion/solicitud solo 'pendiente'/'aprobada'/'rechazada' — perdiendo
 * estados reales del negocio como "donación ya entregada" (completada) o
 * "evento en curso". Esta es una corrección funcional, no cosmética.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── EVENTO: fechaInicio/fechaFin en vez de una sola fechaEntrega ──────
        Schema::table('evento', function (Blueprint $table) {
            $table->date('fechaInicio')->nullable()->after('fechaPublicacion');
            $table->date('fechaFin')->nullable()->after('fechaInicio');
        });

        // Los eventos existentes eran de un solo día: fechaInicio = fechaFin = fechaEntrega
        DB::statement('UPDATE evento SET fechaInicio = fechaEntrega, fechaFin = fechaEntrega');

        Schema::table('evento', function (Blueprint $table) {
            $table->dropColumn('fechaEntrega');
        });

        // Estado de evento: BORRADOR, PUBLICADO, EN_CURSO, FINALIZADO, CANCELADO
        DB::statement("ALTER TABLE evento MODIFY estado ENUM('borrador','publicado','en_curso','finalizado','cancelado') NOT NULL DEFAULT 'borrador'");
        // Los eventos que estaban 'activo' pasan a 'publicado'; 'inactivo' pasa a 'cancelado'
        DB::table('evento')->where('estado', 'activo')->update(['estado' => 'publicado']);
        DB::table('evento')->where('estado', 'inactivo')->update(['estado' => 'cancelado']);

        // ── DONACION: agrega 'cancelada' y 'completada' ───────────────────────
        DB::statement("ALTER TABLE donacion MODIFY estado ENUM('pendiente','aprobada','cancelada','completada','rechazada') NOT NULL DEFAULT 'pendiente'");

        // ── SOLICITUD: agrega 'cancelada' y 'completada' ──────────────────────
        DB::statement("ALTER TABLE solicitud MODIFY estado ENUM('aprobada','pendiente','rechazada','cancelada','completada') NOT NULL DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE solicitud MODIFY estado ENUM('aprobada','rechazada','pendiente') NOT NULL DEFAULT 'pendiente'");
        DB::statement("ALTER TABLE donacion MODIFY estado ENUM('aprobada','rechazada','pendiente') NOT NULL DEFAULT 'pendiente'");

        DB::table('evento')->where('estado', 'cancelado')->update(['estado' => 'inactivo']);
        DB::table('evento')->whereIn('estado', ['borrador', 'publicado', 'en_curso', 'finalizado'])->update(['estado' => 'activo']);
        DB::statement("ALTER TABLE evento MODIFY estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo'");

        Schema::table('evento', function (Blueprint $table) {
            $table->date('fechaEntrega')->nullable()->after('fechaPublicacion');
        });
        DB::statement('UPDATE evento SET fechaEntrega = fechaInicio');
        Schema::table('evento', function (Blueprint $table) {
            $table->dropColumn(['fechaInicio', 'fechaFin']);
        });
    }
};
