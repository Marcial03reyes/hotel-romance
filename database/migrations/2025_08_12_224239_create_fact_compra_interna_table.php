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
        Schema::create('fact_compra_interna', function (Blueprint $table) {
            $table->id('id_compra_interna');
            $table->unsignedBigInteger('id_prod_bod');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 8, 2);
            $table->date('fecha_compra');
            $table->text('proveedor')->nullable();
            $table->timestamps();

            // Clave foránea
            $table->foreign('id_prod_bod')->references('id_prod_bod')->on('dim_productos_bodega')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_compra_interna');
    }
};