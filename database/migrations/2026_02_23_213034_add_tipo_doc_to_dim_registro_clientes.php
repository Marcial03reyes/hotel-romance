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
            if (!Schema::hasColumn('dim_registro_clientes', 'tipo_doc')) {
                $table->string('tipo_doc', 20)->default('DNI')->after('nombre_apellido');
            }
        });
    }
};
