<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('idUsuario');
            $table->string('nombre', 100);
            $table->enum('tipoDocumento', ['CC', 'TI', 'CE', 'Pasaporte']);
            $table->string('numDocumento', 15)->unique();
            $table->date('fechaNacimiento');
            $table->string('direccion', 255);
            $table->string('email', 150)->unique();
            $table->string('contrasena', 255);
            $table->string('telefono', 10);
            $table->enum('rol', ['administrador', 'asistente', 'donante'])->default('donante');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->text('necesidad')->nullable();
            $table->enum('prioridad', ['alta', 'media', 'baja'])->nullable();
            $table->text('observacion_visita')->nullable();
            $table->string('reset_token', 64)->nullable();
            $table->dateTime('reset_token_expira')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
