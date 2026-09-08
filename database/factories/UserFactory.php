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
            'nombre'          => fake()->firstName(),
            'apellido'        => fake()->lastName(),
            'tipoDocumento'   => 'CC',
            'numDocumento'    => fake()->unique()->numerify('##########'),
            'fechaNacimiento' => fake()->dateTimeBetween('-70 years', '-14 years')->format('Y-m-d'),
            'direccion'       => fake()->address(),
            'email'           => fake()->unique()->safeEmail(),
            'contrasena'      => Hash::make('password'),
            'telefono'        => fake()->numerify('3#########'),
            'rol'             => 'donante',
            'estado'          => 'activo',
        ];
    }
}
