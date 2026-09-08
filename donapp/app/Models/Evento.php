<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Evento
 *
 * Ya no existen las tablas `publicacion` ni `ProgramadorEventos`: el
 * contenido que se publicaba sobre el evento (título/contenido/imagen) y su
 * programación (fecha y lugar de entrega) ahora son columnas propias de
 * `evento`, tal como lo define el diagrama de clases actualizado.
 */
class Evento extends Model
{
    protected $table      = 'evento';
    protected $primaryKey = 'idEvento';
    public    $timestamps = false;

    protected $fillable = [
        'Nombre', 'contenido', 'imagen', 'fechaPublicacion',
        'fechaInicio', 'fechaFin', 'lugar', 'estado',
    ];

    public function isActivo(): bool
    {
        return in_array($this->estado, ['publicado', 'en_curso']);
    }

    public function esMultidia(): bool
    {
        return $this->fechaInicio && $this->fechaFin && $this->fechaInicio !== $this->fechaFin;
    }

    /**
     * Retorna la imagen como base64 para mostrar en HTML.
     */
    public function imagenBase64(): ?string
    {
        return $this->imagen ? 'data:image/jpeg;base64,' . base64_encode($this->imagen) : null;
    }
}
