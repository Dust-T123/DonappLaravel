<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo: VisitaDomiciliaria
 *
 * Un beneficiario/donante solicita que un asistente o administrador
 * programe una visita a su domicilio (por ejemplo, para verificar una
 * necesidad reportada). Queda en estado 'pendiente' hasta que un
 * administrador o asistente la aprueba, rechaza o marca como realizada.
 */
class VisitaDomiciliaria extends Model
{
    protected $table      = 'visita_domiciliaria';
    protected $primaryKey = 'idVisita';
    public    $timestamps = false;

    protected $fillable = [
        'idUsuario', 'idGestor', 'direccion', 'motivo',
        'fechaPreferida', 'estado', 'observacion', 'fechaResolucion',
    ];

    protected $casts = [
        'fechaSolicitud'  => 'datetime',
        'fechaResolucion' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }

    public function gestor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'idGestor');
    }

    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }
}
