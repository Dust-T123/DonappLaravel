<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    protected $table      = 'evento';
    protected $primaryKey = 'idEvento';
    public    $timestamps = false;

    protected $fillable = ['Nombre', 'estado'];

    public function programacion(): HasOne
    {
        return $this->hasOne(ProgramadorEventos::class, 'idEvento');
    }

    public function publicaciones(): HasMany
    {
        return $this->hasMany(Publicacion::class, 'idEvento');
    }

    public function publicacion(): HasOne
    {
        return $this->hasOne(Publicacion::class, 'idEvento');
    }

    public function isActivo(): bool
    {
        return $this->estado === 'activo';
    }
}
