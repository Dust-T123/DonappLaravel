<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Tabla que soporta el flujo de "Datos Sensibles": solicitudes de corrección
 * de campos que un usuario ya no puede editar directamente (nombre,
 * tipoDocumento, numDocumento, fechaNacimiento). Quedan en estado 'pendiente'
 * hasta que un administrador DISTINTO de quien la solicitó la aprueba o
 * rechaza (ver AdminController::aprobarCorreccion/rechazarCorreccion).
 *
 * Se reconstruyó este esquema a partir de las columnas que ya usa el código
 * (CorreccionDatos::$fillable/$casts y los controladores Admin/Asis/User),
 * porque la tabla y el trigger existían solo en la base de datos local de
 * quien construyó el feature y nunca se habían versionado en una migración.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('correccion_datos', function (Blueprint $table) {
            $table->id('idCorreccion');

            $table->unsignedBigInteger('idUsuario');      // a quién pertenecen los datos
            $table->unsignedBigInteger('idSolicitante');  // quién pidió el cambio
            $table->unsignedBigInteger('idAprobador')->nullable(); // quién lo resolvió

            $table->enum('campo', ['nombre', 'tipoDocumento', 'numDocumento', 'fechaNacimiento']);
            $table->string('valorAnterior', 150);
            $table->string('valorNuevo', 150);
            $table->string('justificacion', 300);

            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');

            $table->dateTime('fechaSolicitud')->useCurrent();
            $table->dateTime('fechaResolucion')->nullable();
            $table->string('observacionResolucion', 300)->nullable();

            // Soporte documental (foto/PDF del documento de identidad), en disco
            // privado. Se purga físicamente al resolver la solicitud (purgarSoporte()),
            // por eso estas columnas quedan nullable.
            $table->string('soporteRuta')->nullable();
            $table->string('soporteMime', 100)->nullable();
            $table->char('soporteHash', 64)->nullable(); // sha256 del archivo al momento de subirlo

            $table->boolean('consentimiento')->default(false);

            $table->foreign('idUsuario')->references('idUsuario')->on('usuario')->onDelete('cascade');
            $table->foreign('idSolicitante')->references('idUsuario')->on('usuario')->onDelete('cascade');
            $table->foreign('idAprobador')->references('idUsuario')->on('usuario')->onDelete('set null');

            $table->index(['idUsuario', 'campo', 'estado']);
        });

        // Evita que se acumulen varias solicitudes pendientes para el mismo
        // usuario+campo. El controlador captura el QueryException que este
        // trigger dispara y lo traduce a un mensaje amigable.
        DB::unprepared('
            CREATE TRIGGER tg_evitar_correccion_duplicada
            BEFORE INSERT ON correccion_datos
            FOR EACH ROW
            BEGIN
                IF NEW.estado = "pendiente" AND EXISTS (
                    SELECT 1 FROM correccion_datos
                    WHERE idUsuario = NEW.idUsuario
                      AND campo = NEW.campo
                      AND estado = "pendiente"
                ) THEN
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Ya existe una solicitud de correccion pendiente para ese campo.";
                END IF;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tg_evitar_correccion_duplicada');
        Schema::dropIfExists('correccion_datos');
    }
};
