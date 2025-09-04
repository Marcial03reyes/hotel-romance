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
        Schema::table('fact_gastos_generales', function (Blueprint $table) {
            // Verificar si las columnas existen antes de eliminarlas
            if (Schema::hasColumn('fact_gastos_generales', 'tipo_comprobante')) {
                $table->dropColumn('tipo_comprobante');
            }
            if (Schema::hasColumn('fact_gastos_generales', 'codigo_comprobante')) {
                $table->dropColumn('codigo_comprobante');
            }
            if (Schema::hasColumn('fact_gastos_generales', 'comprobante')) {
                $table->dropColumn('comprobante');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fact_gastos_generales', function (Blueprint $table) {
            $table->enum('tipo_comprobante', ['BOLETA', 'FACTURA', 'NINGUNO'])->nullable()->after('fecha_gasto');
            $table->string('codigo_comprobante', 50)->nullable()->after('tipo_comprobante');
            $table->string('comprobante')->nullable()->after('codigo_comprobante');
        });
    }
};