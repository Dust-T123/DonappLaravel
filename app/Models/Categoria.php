<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table      = 'categoria';
    protected $primaryKey = 'idCategoria';
    public    $timestamps = false;

    protected $fillable = ['nombre', 'idUsuario'];

    public function creadaPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }

    public function donaciones(): HasMany
    {
        return $this->hasMany(Donacion::class, 'idCategoria');
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'idCategoria');
    }
}
