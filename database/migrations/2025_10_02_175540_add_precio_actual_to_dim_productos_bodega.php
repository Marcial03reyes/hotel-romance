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
        Schema::table('dim_productos_bodega', function (Blueprint $table) {
            // Agregar precio_actual después de la columna nombre
            $table->decimal('precio_actual', 10, 2)->default(0.00)->after('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dim_productos_bodega', function (Blueprint $table) {
            $table->dropColumn('precio_actual');
        });
    }
};