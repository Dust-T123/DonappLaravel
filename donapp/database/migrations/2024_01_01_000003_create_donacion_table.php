<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donacion', function (Blueprint $table) {
            $table->id('idDonacion');
            $table->text('descripcion');
            $table->binary('imagen')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado', 'entregado'])->default('pendiente');
            $table->unsignedInteger('stock')->default(1);
            $table->text('observacion')->nullable();
            $table->unsignedBigInteger('idCategoria')->nullable();

            $table->foreign('idCategoria')
                  ->references('idCategoria')
                  ->on('categoria')
                  ->nullOnDelete();
        });

        Schema::create('DonacionUsuario', function (Blueprint $table) {
            $table->date('FechaCreacion');
            $table->unsignedBigInteger('idDonante');
            $table->unsignedBigInteger('idDonacion');

            $table->primary(['idDonante', 'idDonacion']);

            $table->foreign('idDonante')
                  ->references('idUsuario')
                  ->on('usuario')
                  ->cascadeOnDelete();

            $table->foreign('idDonacion')
                  ->references('idDonacion')
                  ->on('donacion')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DonacionUsuario');
        Schema::dropIfExists('donacion');
    }
};
