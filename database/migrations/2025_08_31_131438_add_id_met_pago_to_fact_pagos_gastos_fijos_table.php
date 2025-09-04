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
        Schema::table('fact_pagos_gastos_fijos', function (Blueprint $table) {
            // Agregar la columna id_met_pago después de monto_pagado
            $table->unsignedBigInteger('id_met_pago')->after('monto_pagado');
            
            // Crear la relación foránea con dim_met_pago
            $table->foreign('id_met_pago')->references('id_met_pago')->on('dim_met_pago');
            
            // Agregar índice para mejorar performance en consultas
            $table->index('id_met_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fact_pagos_gastos_fijos', function (Blueprint $table) {
            // Eliminar la relación foránea primero
            $table->dropForeign(['id_met_pago']);
            
            // Eliminar el índice
            $table->dropIndex(['id_met_pago']);
            
            // Eliminar la columna
            $table->dropColumn('id_met_pago');
        });
    }
};