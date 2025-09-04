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
            $table->enum('tipo_comprobante', ['BOLETA', 'FACTURA', 'NINGUNO'])
                  ->default('NINGUNO')
                  ->after('fecha_gasto');
            $table->string('codigo_comprobante', 50)
                  ->nullable()
                  ->after('tipo_comprobante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fact_gastos_generales', function (Blueprint $table) {
            $table->dropColumn(['tipo_comprobante', 'codigo_comprobante']);
        });
    }
};