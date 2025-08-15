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
        Schema::create('fact_trabajadores', function (Blueprint $table) {
            $table->string('DNI', 20)->primary();
            $table->string('nombre_apellido', 100);
            $table->decimal('sueldo', 6, 2);
            $table->date('Fecha_inicio');
            $table->string('Telef', 9);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_trabajadores');
    }
};
