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
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->string('recurso')->nullable(); // Archivo adjunto
            $table->foreignId('curso_id')->constrained('cursos')->onDelete('cascade'); // Curso al que pertenece la tarea
            $table->foreignId('profesor_id')->constrained('usuarios')->onDelete('cascade'); // Profesor que asigna la tarea
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
