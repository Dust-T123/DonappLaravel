<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramadorEventos extends Model
{
    protected $table      = 'ProgramadorEventos';
    protected $primaryKey = 'idEvento';
    public    $timestamps = false;
    public    $incrementing = false;

    protected $fillable = ['idEvento', 'FechaEntrega', 'Lugar'];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'idEvento');
    }
}
