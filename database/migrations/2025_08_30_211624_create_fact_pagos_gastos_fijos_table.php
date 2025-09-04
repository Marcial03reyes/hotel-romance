<?php
// database/migrations/2025_08_30_000002_create_fact_pagos_gastos_fijos_table.php

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
        Schema::create('fact_pagos_gastos_fijos', function (Blueprint $table) {
            $table->id('id_pago_gasto');
            $table->unsignedBigInteger('id_gasto_fijo');
            $table->integer('mes');     // Mes del pago (1-12)
            $table->integer('anio');    // Año del pago
            $table->decimal('monto_pagado', 8, 2); // Monto que se pagó (puede diferir del fijo)
            $table->string('comprobante')->nullable(); // Ruta del archivo PDF/PNG
            $table->date('fecha_pago'); // Fecha en que se registró el pago
            $table->timestamps(); // created_at, updated_at
            
            // Relación con gastos fijos
            $table->foreign('id_gasto_fijo')->references('id_gasto_fijo')->on('fact_gastos_fijos')->onDelete('cascade');
            
            // Índice único para evitar pagos duplicados del mismo servicio en el mismo mes/año
            $table->unique(['id_gasto_fijo', 'mes', 'anio'], 'unique_pago_fijo_mes_anio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_pagos_gastos_fijos');
    }
};