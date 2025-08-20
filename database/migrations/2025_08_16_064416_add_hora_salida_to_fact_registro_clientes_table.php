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
        Schema::table('fact_registro_clientes', function (Blueprint $table) {
            // Agregar hora_salida después de hora_ingreso
            $table->time('hora_salida')->nullable()->after('hora_ingreso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fact_registro_clientes', function (Blueprint $table) {
            $table->dropColumn('hora_salida');
        });
    }
};