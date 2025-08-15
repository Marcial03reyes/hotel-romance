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
        Schema::table('fact_compra_interna', function (Blueprint $table) {
            // Verificar si las columnas no existen antes de agregarlas
            if (!Schema::hasColumn('fact_compra_interna', 'fecha_compra')) {
                $table->date('fecha_compra')->nullable()->after('precio_unitario');
            }
            
            if (!Schema::hasColumn('fact_compra_interna', 'proveedor')) {
                $table->string('proveedor', 255)->nullable()->after('fecha_compra');
            }
            
            // Agregar timestamps solo si no existen
            if (!Schema::hasColumn('fact_compra_interna', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fact_compra_interna', function (Blueprint $table) {
            // Eliminar las columnas agregadas
            $table->dropColumn(['fecha_compra', 'proveedor']);
            $table->dropTimestamps();
        });
    }
};