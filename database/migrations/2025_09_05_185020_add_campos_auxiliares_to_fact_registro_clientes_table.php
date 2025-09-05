<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Agrega campos auxiliares para manejar fecha/hora real de ingreso
     * Solo se usan para turno NOCHE cuando el registro cruza dos fechas
     */
    public function up(): void
    {
        Schema::table('fact_registro_clientes', function (Blueprint $table) {
            // Campos auxiliares para turno NOCHE solamente
            $table->date('fecha_ingreso_real')->nullable()->after('fecha_ingreso')->comment('Fecha real de ingreso del cliente (solo turno NOCHE)');
            $table->time('hora_ingreso_real')->nullable()->after('hora_ingreso')->comment('Hora real de ingreso del cliente (solo turno NOCHE)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fact_registro_clientes', function (Blueprint $table) {
            $table->dropColumn(['fecha_ingreso_real', 'hora_ingreso_real']);
        });
    }
};