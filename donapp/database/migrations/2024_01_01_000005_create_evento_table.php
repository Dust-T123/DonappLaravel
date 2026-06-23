<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento', function (Blueprint $table) {
            $table->id('idEvento');
            $table->string('Nombre', 150);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
        });

        Schema::create('ProgramadorEventos', function (Blueprint $table) {
            $table->unsignedBigInteger('idEvento')->primary();
            $table->date('FechaEntrega');
            $table->string('Lugar', 200);

            $table->foreign('idEvento')
                  ->references('idEvento')
                  ->on('evento')
                  ->cascadeOnDelete();
        });

        Schema::create('publicacion', function (Blueprint $table) {
            $table->id('idPublicacion');
            $table->string('titulo', 200);
            $table->text('contenido');
            $table->binary('imagen')->nullable();
            $table->date('fechaPublicacion');
            $table->unsignedBigInteger('idUsuario')->nullable();
            $table->unsignedBigInteger('idEvento')->nullable();

            $table->foreign('idUsuario')
                  ->references('idUsuario')
                  ->on('usuario')
                  ->nullOnDelete();

            $table->foreign('idEvento')
                  ->references('idEvento')
                  ->on('evento')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publicacion');
        Schema::dropIfExists('ProgramadorEventos');
        Schema::dropIfExists('evento');
    }
};
