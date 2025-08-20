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
        Schema::table('fact_pago_prod', function (Blueprint $table) {
            $table->enum('comprobante', ['SI', 'NO'])->default('NO')->after('id_met_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fact_pago_prod', function (Blueprint $table) {
            $table->dropColumn('comprobante');
        });
    }
};