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
        Schema::create('fact_registro_clientes', function (Blueprint $table) {
            $table->id('id_estadia');
            $table->time('hora_ingreso');
            $table->date('fecha_ingreso');
            $table->enum('habitacion', [201,202,203,301,302,303]);
            $table->string('doc_identidad', 20);
            $table->foreign('doc_identidad')->references('doc_identidad')->on('dim_registro_clientes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_registro_clientes');
    }
};
