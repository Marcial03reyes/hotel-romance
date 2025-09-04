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
        Schema::create('dim_met_pago', function (Blueprint $table) {
            $table->id('id_met_pago');
            $table->enum('met_pago', ['Efectivo', 'Plin', 'Tarjeta crédito', 'Transferencia', 'Yape']);
            // $table->enum('met_pago', ['Efectivo', 'Yape', 'Plin', 'Tarjeta', 'QR']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dim_met_pago');
    }
};
