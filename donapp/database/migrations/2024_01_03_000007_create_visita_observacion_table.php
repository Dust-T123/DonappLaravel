<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Antes, `visita_domiciliaria.observacion` era un solo campo que cada
 * administrador/asistente sobrescribía al gestionar la visita, perdiendo lo
 * que había escrito otra persona antes. Esta tabla lo convierte en una
 * bitácora real: cada nota queda registrada por separado, con quién la
 * escribió y cuándo. Los 3 roles (donante, asistente, administrador) pueden
 * agregar notas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visita_observacion', function (Blueprint $table) {
            $table->id('idObservacion');
            $table->unsignedBigInteger('idVisita');
            $table->unsignedBigInteger('idUsuario')->nullable(); // quién escribió la nota
            $table->string('texto', 500);
            $table->timestamp('fecha')->useCurrent();

            $table->foreign('idVisita')->references('idVisita')->on('visita_domiciliaria')->cascadeOnDelete();
            $table->foreign('idUsuario')->references('idUsuario')->on('usuario')->nullOnDelete();
        });

        // Migra la observación que ya existiera como la primera nota del historial.
        $visitas = DB::table('visita_domiciliaria')->whereNotNull('observacion')->where('observacion', '<>', '')->get();
        foreach ($visitas as $v) {
            DB::table('visita_observacion')->insert([
                'idVisita'   => $v->idVisita,
                'idUsuario'  => $v->idGestor,
                'texto'      => $v->observacion,
                'fecha'      => $v->fechaResolucion ?? $v->fechaSolicitud ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('visita_observacion');
    }
};
