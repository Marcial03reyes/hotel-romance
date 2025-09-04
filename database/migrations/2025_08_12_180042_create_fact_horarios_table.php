<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fact_horarios', function (Blueprint $table) {
            $table->id();
            $table->string('DNI', 20); // Relaciona con FACT_TRABAJADORES
            $table->enum('dia_semana', ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo']);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->boolean('activo')->default(true); // Para activar/desactivar horarios
            $table->timestamps();
            
            // Relación con la tabla de trabajadores
            $table->foreign('DNI')->references('DNI')->on('fact_trabajadores')->onDelete('cascade');
            
            // Índice único para evitar duplicados (un trabajador no puede tener dos horarios el mismo día)
            $table->unique(['DNI', 'dia_semana']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fact_horarios');
    }
};