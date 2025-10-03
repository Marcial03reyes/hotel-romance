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
        Schema::table('fact_pago_prod', function (Blueprint $table) {
            // 1. Hacer id_estadia nullable
            $table->unsignedBigInteger('id_estadia')->nullable()->change();
            
            // 2. Agregar nuevos campos
            $table->date('fecha_venta')->after('id_estadia');
            $table->tinyInteger('turno')->comment('0=DÍA, 1=NOCHE')->after('fecha_venta');
            
            // 3. Agregar índices para optimizar consultas de análisis
            $table->index('fecha_venta');
            $table->index('turno');
            $table->index(['fecha_venta', 'turno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fact_pago_prod', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex(['fact_pago_prod_fecha_venta_index']);
            $table->dropIndex(['fact_pago_prod_turno_index']);
            $table->dropIndex(['fact_pago_prod_fecha_venta_turno_index']);
            
            // Eliminar columnas
            $table->dropColumn(['fecha_venta', 'turno']);
            
            // Restaurar id_estadia como NOT NULL (si había datos, esto podría fallar)
            $table->unsignedBigInteger('id_estadia')->nullable(false)->change();
        });
    }
};