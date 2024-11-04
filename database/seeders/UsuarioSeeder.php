<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Carbon\Carbon;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Usuario::create([
            'nombre' => 'Admin',
            'correo' => 'admin123@juancitopinto.com',
            'contraseña' => bcrypt('123456'), // Laravel automáticamente cifra la contraseña
            'rol' => 'administrador',
            'ci' => '123456789', // Valor único para CI
            'fecha_registro' => Carbon::now(), // Fecha actual
            'materia' => null, // No necesario para admin
            'curso_id' => null, // No necesario para admin
        ]);
    }
}
