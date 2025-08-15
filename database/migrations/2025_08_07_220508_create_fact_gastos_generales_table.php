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
        Schema::create('fact_gastos_generales', function (Blueprint $table) {
            $table->id('id_gasto');
            $table->unsignedBigInteger('id_tipo_gasto');
            $table->unsignedBigInteger('id_met_pago');
            $table->decimal('monto', 8, 2);
            $table->date('fecha_gasto');
            $table->foreign('id_tipo_gasto')->references('id_tipo_gasto')->on('dim_tipo_gasto');
            $table->foreign('id_met_pago')->references('id_met_pago')->on('dim_met_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_gastos_generales');
    }
};
