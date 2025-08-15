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
        Schema::create('fact_pago_prod', function (Blueprint $table) {
            $table->id('id_compra');
            $table->unsignedBigInteger('id_estadia');
            $table->unsignedBigInteger('id_prod_bod');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 6, 2);
            $table->unsignedBigInteger('id_met_pago');
            $table->foreign('id_estadia')->references('id_estadia')->on('fact_registro_clientes');
            $table->foreign('id_prod_bod')->references('id_prod_bod')->on('dim_productos_bodega');
            $table->foreign('id_met_pago')->references('id_met_pago')->on('dim_met_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_pago_prod');
    }
};
