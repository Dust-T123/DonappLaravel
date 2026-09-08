<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * El nuevo diagrama de clases ya no contempla las clases `Publicación` ni
 * `ProgramadorEventos`: lo que publicaba un evento (título/contenido/imagen)
 * y su programación (fecha y lugar de entrega) pasan a ser columnas propias
 * de `evento`. Esta migración agrega esas columnas, traslada los datos que
 * ya existieran y elimina las dos tablas que quedan obsoletas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evento', function (Blueprint $table) {
            $table->text('contenido')->nullable()->after('Nombre');
            $table->binary('imagen')->nullable()->after('contenido');
            $table->date('fechaPublicacion')->nullable()->after('imagen');
            $table->date('fechaEntrega')->nullable()->after('fechaPublicacion');
            $table->string('lugar', 255)->nullable()->after('fechaEntrega');
        });

        if (Schema::hasTable('publicacion') && Schema::hasTable('ProgramadorEventos')) {
            DB::statement('
                UPDATE evento e
                LEFT JOIN publicacion p ON p.idEvento = e.idEvento
                LEFT JOIN ProgramadorEventos pe ON pe.idEvento = e.idEvento
                SET e.contenido        = p.contenido,
                    e.imagen           = p.imagen,
                    e.fechaPublicacion = p.fechaPublicacion,
                    e.fechaEntrega     = pe.FechaEntrega,
                    e.lugar            = pe.Lugar
            ');
        }

        Schema::dropIfExists('publicacion');
        Schema::dropIfExists('ProgramadorEventos');
    }

    public function down(): void
    {
        Schema::create('ProgramadorEventos', function (Blueprint $table) {
            $table->unsignedBigInteger('idEvento')->primary();
            $table->date('FechaEntrega');
            $table->string('Lugar', 200);
            $table->foreign('idEvento')->references('idEvento')->on('evento')->cascadeOnDelete();
        });

        Schema::create('publicacion', function (Blueprint $table) {
            $table->id('idPublicacion');
            $table->string('titulo', 200);
            $table->text('contenido');
            $table->binary('imagen')->nullable();
            $table->date('fechaPublicacion');
            $table->unsignedBigInteger('idUsuario')->nullable();
            $table->unsignedBigInteger('idEvento')->nullable();
            $table->foreign('idUsuario')->references('idUsuario')->on('usuario')->nullOnDelete();
            $table->foreign('idEvento')->references('idEvento')->on('evento')->nullOnDelete();
        });

        Schema::table('evento', function (Blueprint $table) {
            $table->dropColumn(['contenido', 'imagen', 'fechaPublicacion', 'fechaEntrega', 'lugar']);
        });
    }
};
