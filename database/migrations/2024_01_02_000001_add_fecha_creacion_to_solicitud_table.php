<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * La tabla `solicitud` nunca tuvo columna de fecha de creación, aunque el
 * modelo Solicitud la declara en $fillable y varias vistas (historial de
 * cliente, dashboard del donante, módulo de reportes) intentan leerla o
 * filtrar por ella — siempre devolvía null. Esta migración agrega la
 * columna y rellena los registros existentes con la fecha actual como
 * mejor aproximación posible (no hay forma de reconstruir la fecha real
 * de creación de solicitudes ya insertadas sin esta columna).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitud', function (Blueprint $table) {
            $table->date('fechaCreacion')->nullable()->after('idGestor');
        });

        DB::table('solicitud')
            ->whereNull('fechaCreacion')
            ->update(['fechaCreacion' => now()->format('Y-m-d')]);
    }

    public function down(): void
    {
        Schema::table('solicitud', function (Blueprint $table) {
            $table->dropColumn('fechaCreacion');
        });
    }
};
