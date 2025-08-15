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
        Schema::create('fact_compra_bodega', function (Blueprint $table) {
            $table->id('id_compra_bodega');
            $table->unsignedBigInteger('id_prod_bod');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 6, 2);
            $table->date('fecha_compra');
            $table->string('proveedor', 255)->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('id_prod_bod')->references('id_prod_bod')->on('dim_productos_bodega');
            
            // Índices para mejorar rendimiento
            $table->index(['id_prod_bod', 'fecha_compra']);
            $table->index('fecha_compra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_compra_bodega');
    }
};