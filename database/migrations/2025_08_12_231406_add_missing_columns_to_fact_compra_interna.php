<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fact_compra_interna', function (Blueprint $table) {
            // Solo agregar las columnas que faltan, sin renombrar
            if (!Schema::hasColumn('fact_compra_interna', 'fecha_compra')) {
                $table->date('fecha_compra')->default(now());
            }
            if (!Schema::hasColumn('fact_compra_interna', 'proveedor')) {
                $table->text('proveedor')->nullable();
            }
            if (!Schema::hasColumn('fact_compra_interna', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('fact_compra_interna', function (Blueprint $table) {
            $table->dropColumn(['fecha_compra', 'proveedor', 'created_at', 'updated_at']);
        });
    }
};