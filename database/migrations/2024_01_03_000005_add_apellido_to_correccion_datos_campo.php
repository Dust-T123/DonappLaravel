<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * `apellido` es ahora un campo sensible de identidad igual que `nombre`,
 * así que también debe poder corregirse a través del flujo de
 * correccion_datos (habeas data).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE correccion_datos MODIFY campo ENUM('nombre','apellido','tipoDocumento','numDocumento','fechaNacimiento') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE correccion_datos MODIFY campo ENUM('nombre','tipoDocumento','numDocumento','fechaNacimiento') NOT NULL");
    }
};
