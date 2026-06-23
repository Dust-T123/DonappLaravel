<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition(): array
    {
        return [
            'nombre'          => fake()->name(),
            'tipoDocumento'   => 'CC',
            'numDocumento'    => fake()->unique()->numerify('##########'),
            'fechaNacimiento' => fake()->date(),
            'direccion'       => fake()->address(),
            'email'           => fake()->unique()->safeEmail(),
            'contrasena'      => Hash::make('password'),
            'telefono'        => fake()->numerify('3#########'),
            'rol'             => 'donante',
            'estado'          => 'activo',
        ];
    }
}
