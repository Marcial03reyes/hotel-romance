<?php
// database/migrations/2025_08_30_000001_create_fact_gastos_fijos_table.php

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
        Schema::create('fact_gastos_fijos', function (Blueprint $table) {
            $table->id('id_gasto_fijo');
            $table->string('nombre_servicio', 100); // Internet, Agua, Luz, etc.
            $table->integer('dia_vencimiento'); // Día del mes (1-31)
            $table->decimal('monto_fijo', 8, 2); // Monto fijo del servicio
            $table->boolean('activo')->default(true); // Para activar/desactivar servicios
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_gastos_fijos');
    }
};