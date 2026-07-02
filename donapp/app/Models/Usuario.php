<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Modelo: Usuario
 *
 * @property int    $idUsuario
 * @property string $nombre
 * @property string $tipoDocumento
 * @property int    $numDocumento
 * @property string $fechaNacimiento
 * @property string $direccion
 * @property string $email
 * @property string $contrasena
 * @property string $telefono
 * @property string $rol            administrador|asistente|donante
 * @property string $estado         activo|inactivo
 * @property string|null $necesidad
 * @property string|null $prioridad
 * @property string|null $observacion_visita
 * @property string|null $reset_token
 * @property string|null $reset_token_expira
 */
class Usuario extends Model
{
    protected $table      = 'usuario';
    protected $primaryKey = 'idUsuario';
    public    $timestamps = false;

    protected $fillable = [
        'nombre', 'tipoDocumento', 'numDocumento', 'fechaNacimiento',
        'direccion', 'email', 'contrasena', 'telefono', 'rol', 'estado',
        'necesidad', 'prioridad', 'observacion_visita',
        'reset_token', 'reset_expira',
    ];

    protected $hidden = ['contrasena', 'reset_token'];

    // ── RELACIONES ────────────────────────────────────────────────────────────

    public function donaciones(): BelongsToMany
    {
        return $this->belongsToMany(
            Donacion::class,
            'DonacionUsuario',
            'idDonante',
            'idDonacion'
        )->withPivot('FechaCreacion');
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'idSolicitante');
    }

    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class, 'idUsuario');
    }

    public function publicaciones(): HasMany
    {
        return $this->hasMany(Publicacion::class, 'idUsuario');
    }

    // ── SCOPES ────────────────────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeRol($query, string $rol)
    {
        return $query->where('rol', $rol);
    }

    // ── HELPERS ───────────────────────────────────────────────────────────────

    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }

    public function esAsistente(): bool
    {
        return $this->rol === 'asistente';
    }

    public function esDonante(): bool
    {
        return $this->rol === 'donante';
    }

    public function verificarContrasena(string $plain): bool
    {
        return password_verify($plain, $this->contrasena);
    }
}
