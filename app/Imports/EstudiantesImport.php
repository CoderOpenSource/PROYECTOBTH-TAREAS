<?php

namespace App\Imports;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class EstudiantesImport implements ToModel
{
    protected $curso_id;

    public function __construct($curso_id)
    {
        $this->curso_id = $curso_id;
    }

    /**
     * Definir el modelo para cada fila del Excel.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Generar el correo eliminando espacios, convirtiendo a minúsculas y añadiendo el dominio
        $nombre = strtolower(str_replace(' ', '', $row[1]));
        $correo = $nombre . '@juancitopinto.com'; // Cambia el dominio como solicitado

        return new Usuario([
            'ci' => $row[0], // La primera columna: Carnet de identidad
            'nombre' => $row[1], // La segunda columna: Nombre del estudiante
            'correo' => $correo, // Correo generado basado en el nombre
            'contraseña' => Hash::make($row[0]), // Contraseña basada en el carnet y hasheada
            'rol' => 'estudiante', // Rol asignado
            'fecha_registro' => now(), // Fecha de registro actual
            'curso_id' => $this->curso_id, // ID del curso asignado
        ]);
    }
}
