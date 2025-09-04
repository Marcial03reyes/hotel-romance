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
        Schema::create('fact_inversiones', function (Blueprint $table) {
            $table->id('id_inversion');
            $table->string('detalle', 255);
            $table->decimal('monto', 8, 2);
            $table->unsignedBigInteger('id_met_pago');
            $table->date('fecha_inversion');
            
            // Índices y claves foráneas
            $table->index('id_met_pago');
            $table->index('fecha_inversion');
            $table->foreign('id_met_pago')->references('id_met_pago')->on('dim_met_pago')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_inversiones');
    }
};