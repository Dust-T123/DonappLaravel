<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla que soporta el nuevo módulo de "Visitas Domiciliarias": un
 * beneficiario/donante puede solicitar que un asistente o administrador
 * visite su domicilio (por ejemplo, para verificar en persona una
 * necesidad reportada). Queda 'pendiente' hasta que un asistente o
 * administrador la aprueba, rechaza, marca como realizada o la cancela.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visita_domiciliaria', function (Blueprint $table) {
            $table->id('idVisita');

            $table->unsignedBigInteger('idUsuario');       // quién solicita la visita
            $table->unsignedBigInteger('idGestor')->nullable(); // asistente/admin que la resuelve

            $table->string('direccion', 255);
            $table->string('motivo', 300);
            $table->date('fechaPreferida')->nullable();

            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'realizada', 'cancelada'])
                  ->default('pendiente');
            $table->string('observacion', 300)->nullable();

            $table->timestamp('fechaSolicitud')->useCurrent();
            $table->dateTime('fechaResolucion')->nullable();

            $table->foreign('idUsuario')->references('idUsuario')->on('usuario')->cascadeOnDelete();
            $table->foreign('idGestor')->references('idUsuario')->on('usuario')->nullOnDelete();

            $table->index(['idUsuario', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visita_domiciliaria');
    }
};
