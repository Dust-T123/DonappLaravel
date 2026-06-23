<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publicacion extends Model
{
    protected $table      = 'publicacion';
    protected $primaryKey = 'idPublicacion';
    public    $timestamps = false;

    protected $fillable = [
        'titulo', 'contenido', 'imagen', 'fechaPublicacion', 'idUsuario', 'idEvento',
    ];

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'idEvento');
    }

    public function imagenBase64(): ?string
    {
        return $this->imagen ? 'data:image/jpeg;base64,' . base64_encode($this->imagen) : null;
    }
}
