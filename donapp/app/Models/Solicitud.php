<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Solicitud extends Model
{
    protected $table      = 'solicitud';
    protected $primaryKey = 'idSolicitud';
    public    $timestamps = false;

    protected $fillable = [
    'descripcion', 'imagen', 'estado', 'observacion',
    'fechaCreacion', 'idSolicitante', 'idCategoria', 'idGestor',
];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'idCategoria');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'idSolicitante');
    }

    public function gestor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'idGestor');
    }

    public function imagenBase64(): ?string
    {
        return $this->imagen ? 'data:image/jpeg;base64,' . base64_encode($this->imagen) : null;
    }
}
