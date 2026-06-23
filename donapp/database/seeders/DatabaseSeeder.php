<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        DB::table('usuario')->insertOrIgnore([
            'nombre' => 'Administrador', 'tipoDocumento' => 'CC',
            'numDocumento' => '1000000000', 'fechaNacimiento' => '1990-01-01',
            'direccion' => 'Sede Principal CES Waldorf', 'email' => 'admin@donapp.co',
            'contrasena' => Hash::make('admin123'), 'telefono' => '3001234567',
            'rol' => 'administrador', 'estado' => 'activo',
        ]);
        // Asistente
        DB::table('usuario')->insertOrIgnore([
            'nombre' => 'Asistente Demo', 'tipoDocumento' => 'CC',
            'numDocumento' => '2000000000', 'fechaNacimiento' => '1995-06-15',
            'direccion' => 'Sede CES Waldorf', 'email' => 'asis@donapp.co',
            'contrasena' => Hash::make('asis123'), 'telefono' => '3117654321',
            'rol' => 'asistente', 'estado' => 'activo',
        ]);
        // Donante
        DB::table('usuario')->insertOrIgnore([
            'nombre' => 'Donante Demo', 'tipoDocumento' => 'CC',
            'numDocumento' => '3000000000', 'fechaNacimiento' => '2000-03-20',
            'direccion' => 'Calle 10 # 5-30, Medellín', 'email' => 'donante@donapp.co',
            'contrasena' => Hash::make('donante123'), 'telefono' => '3209876543',
            'rol' => 'donante', 'estado' => 'activo', 'necesidad' => 'Artículos de primera necesidad',
        ]);

        $adminId = DB::table('usuario')->where('email', 'admin@donapp.co')->value('idUsuario');
        foreach (['Ropa','Alimentos','Juguetes','Útiles escolares','Muebles','Electrodomésticos','Libros','Calzado'] as $cat) {
            DB::table('categoria')->insertOrIgnore(['nombre' => $cat, 'idUsuario' => $adminId]);
        }

        $this->command->info('Datos iniciales listos. Admin: admin@donapp.co / admin123');
    }
}
