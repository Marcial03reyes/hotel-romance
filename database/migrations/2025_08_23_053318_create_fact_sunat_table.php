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
        Schema::create('fact_sunat', function (Blueprint $table) {
            $table->id('id_sunat');
            $table->enum('tipo_comprobante', ['BOLETA', 'FACTURA', 'NINGUNO']);
            $table->string('codigo_comprobante', 50)->nullable();
            $table->decimal('monto', 10, 2);
            $table->date('fecha_comprobante');
            $table->string('archivo_comprobante')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_sunat');
    }
};