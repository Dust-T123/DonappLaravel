<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud', function (Blueprint $table) {
            $table->id('idSolicitud');
            $table->text('descripcion');
            $table->binary('imagen')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'entregado'])->default('pendiente');
            $table->text('observacion')->nullable();
            $table->unsignedBigInteger('idSolicitante')->nullable();
            $table->unsignedBigInteger('idCategoria')->nullable();
            $table->unsignedBigInteger('idGestor')->nullable();

            $table->foreign('idSolicitante')
                  ->references('idUsuario')
                  ->on('usuario')
                  ->nullOnDelete();

            $table->foreign('idCategoria')
                  ->references('idCategoria')
                  ->on('categoria')
                  ->nullOnDelete();

            $table->foreign('idGestor')
                  ->references('idUsuario')
                  ->on('usuario')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud');
    }
};
