<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Se elimina la columna `imagen` de `solicitud`: adjuntar una foto para
 * pedir ayuda no tenía sentido (a diferencia de una donación, donde la foto
 * ayuda a validar el artículo ofrecido) y complicaba el formulario sin
 * necesidad.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitud', function (Blueprint $table) {
            $table->dropColumn('imagen');
        });
    }

    public function down(): void
    {
        Schema::table('solicitud', function (Blueprint $table) {
            $table->binary('imagen')->nullable()->after('descripcion');
        });
    }
};
