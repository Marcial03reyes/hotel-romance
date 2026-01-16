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
        Schema::create('fact_penalidad', function (Blueprint $table) {
            $table->id('id_penalidad');
            $table->unsignedBigInteger('id_estadia');
            $table->unsignedBigInteger('id_met_pago'); // ✅ CORREGIDO: bigint en lugar de int
            $table->decimal('monto', 10, 2);
            $table->timestamp('created_at')->useCurrent();
            
            // Índices para optimizar consultas
            $table->index('id_estadia');
            $table->index('id_met_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_penalidad');
    }
};