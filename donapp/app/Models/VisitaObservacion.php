<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo: VisitaObservacion
 *
 * Una entrada de la bitácora de una visita domiciliaria. Cualquiera de los
 * 3 roles (donante, asistente, administrador) puede agregar una nota; nunca
 * se sobrescriben entre sí.
 */
class VisitaObservacion extends Model
{
    protected $table      = 'visita_observacion';
    protected $primaryKey = 'idObservacion';
    public    $timestamps = false;

    protected $fillable = ['idVisita', 'idUsuario', 'texto', 'fecha'];

    protected $casts = ['fecha' => 'datetime'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }

    public function visita(): BelongsTo
    {
        return $this->belongsTo(VisitaDomiciliaria::class, 'idVisita');
    }
}
