<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Ajustes de `usuario` según el nuevo diagrama de clases:
 *
 *  - Se agrega `apellido` (antes el nombre completo se guardaba todo en
 *    `nombre`).
 *  - `tipoDocumento` gana los valores TE (Tarjeta de Extranjería) y PPT
 *    (Permiso por Protección Temporal), usados por población migrante en
 *    Colombia y que el formulario original no contemplaba.
 *  - `prioridad` gana el valor `urgente`.
 *  - Se agrega una regla de edad mínima: en Colombia, desde los 14 años una
 *    persona empieza a tener capacidad relativa para ciertos actos (Código
 *    de la Infancia y la Adolescencia, Ley 1098 de 2006, y Código Civil
 *    art. 1502-1504). Por debajo de esa edad, el registro y tratamiento de
 *    datos personales requiere representante legal, algo que esta
 *    plataforma no implementa; por eso se exige edad >= 14 tanto a nivel de
 *    aplicación (ver AuthController/AdminController) como a nivel de base
 *    de datos (CHECK, como último cinturón de seguridad).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->string('apellido', 100)->default('')->after('nombre');
        });

        DB::statement("ALTER TABLE usuario MODIFY tipoDocumento ENUM('CC','TI','CE','TE','PPT','Pasaporte') NOT NULL");
        DB::statement("ALTER TABLE usuario MODIFY prioridad ENUM('alta','media','baja','urgente') NULL");

        // CHECK constraint (MySQL >= 8.0.16). Si el motor no lo soporta, la
        // validación en AuthController/AdminController/AsisController sigue
        // aplicando la misma regla a nivel de aplicación.
        try {
            DB::statement('ALTER TABLE usuario ADD CONSTRAINT chk_edad_minima CHECK (TIMESTAMPDIFF(YEAR, fechaNacimiento, CURDATE()) >= 14)');
        } catch (\Throwable $e) {
            // Motor sin soporte de CHECK (o versión antigua de MySQL/MariaDB): se ignora
            // silenciosamente, la validación de aplicación sigue siendo obligatoria.
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE usuario DROP CONSTRAINT chk_edad_minima');
        } catch (\Throwable $e) {
        }

        DB::statement("ALTER TABLE usuario MODIFY prioridad ENUM('alta','media','baja') NULL");
        DB::statement("ALTER TABLE usuario MODIFY tipoDocumento ENUM('CC','TI','CE','Pasaporte') NOT NULL");

        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn('apellido');
        });
    }
};
