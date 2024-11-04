<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->string('contraseña');
            $table->string('rol'); // Roles: admin, profesor, estudiante
            $table->string('ci')->unique(); // Campo CI único
            $table->string('materia')->nullable(); // Solo para profesores
            $table->foreignId('curso_id')->nullable()->constrained('cursos')->onDelete('set null'); // Curso al que pertenece el estudiante
            $table->timestamp('fecha_registro')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
