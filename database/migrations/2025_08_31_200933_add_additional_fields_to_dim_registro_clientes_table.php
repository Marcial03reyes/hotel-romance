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
        Schema::table('dim_registro_clientes', function (Blueprint $table) {
            $table->string('estado_civil', 20)->nullable()->after('nombre_apellido');
            $table->date('fecha_nacimiento')->nullable()->after('estado_civil');
            $table->string('lugar_nacimiento', 100)->nullable()->after('fecha_nacimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dim_registro_clientes', function (Blueprint $table) {
            $table->dropColumn([
                'estado_civil',
                'fecha_nacimiento', 
                'lugar_nacimiento'
            ]);
        });
    }
};