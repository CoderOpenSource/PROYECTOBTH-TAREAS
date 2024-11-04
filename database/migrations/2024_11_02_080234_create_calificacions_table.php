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
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarea_id')->constrained('tareas')->onDelete('cascade');
            $table->foreignId('estudiante_id')->constrained('usuarios')->onDelete('cascade'); // Estudiante que recibe la calificación
            $table->float('calificacion');
            $table->text('comentarios')->nullable();
            $table->foreignId('profesor_id')->constrained('usuarios')->onDelete('cascade'); // Profesor que califica
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
