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
            // Agregar campo observaciones
            $table->text('obs')->nullable()->after('doc_identidad')
                  ->comment('Observaciones adicionales como número de placa, notas especiales, etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fact_registro_clientes', function (Blueprint $table) {
            $table->dropColumn('obs');
        });
    }
};