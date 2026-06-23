<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo: Donacion
 *
 * @property int    $idDonacion
 * @property string $descripcion
 * @property mixed  $imagen        BLOB
 * @property string $estado        pendiente|aprobado|rechazado|entregado
 * @property int    $stock
 * @property string|null $observacion
 * @property int    $idCategoria
 */
class Donacion extends Model
{
    protected $table      = 'donacion';
    protected $primaryKey = 'idDonacion';
    public    $timestamps = false;

    protected $fillable = [
        'descripcion', 'imagen', 'estado', 'stock', 'observacion', 'idCategoria',
    ];

    // ── RELACIONES ────────────────────────────────────────────────────────────

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'idCategoria');
    }

    public function donantes(): BelongsToMany
    {
        return $this->belongsToMany(
            Usuario::class,
            'DonacionUsuario',
            'idDonacion',
            'idDonante'
        )->withPivot('FechaCreacion');
    }

    // ── HELPERS ───────────────────────────────────────────────────────────────

    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Retorna la imagen como base64 para mostrar en HTML.
     */
    public function imagenBase64(): ?string
    {
        return $this->imagen ? 'data:image/jpeg;base64,' . base64_encode($this->imagen) : null;
    }
}
