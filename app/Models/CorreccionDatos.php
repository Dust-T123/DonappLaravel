<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorreccionDatos extends Model
{
    protected $table = 'correccion_datos';
    protected $primaryKey = 'idCorreccion';
    public $timestamps = false;

    protected $fillable = [
        'idUsuario',
        'idSolicitante',
        'campo',
        'valorAnterior',
        'valorNuevo',
        'justificacion',
        'estado',
        'idAprobador',
        'fechaResolucion',
        'observacionResolucion',
        'soporteRuta',
        'soporteMime',
        'soporteHash',
        'consentimiento',
    ];

    protected $casts = [
        'fechaSolicitud'  => 'datetime',
        'fechaResolucion' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario', 'idUsuario');
    }

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'idSolicitante', 'idUsuario');
    }

    public function aprobador()
    {
        return $this->belongsTo(Usuario::class, 'idAprobador', 'idUsuario');
    }
}