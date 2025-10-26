<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatosPersonalesToDimRegistroClientesTable extends Migration
{
    public function up()
    {
        Schema::table('dim_registro_clientes', function (Blueprint $table) {
            $table->enum('sexo', ['M', 'F'])->nullable()->after('estado_civil');
            $table->string('profesion_ocupacion', 100)->nullable()->after('sexo');
        });
    }

    public function down()
    {
        Schema::table('dim_registro_clientes', function (Blueprint $table) {
            $table->dropColumn(['sexo', 'profesion_ocupacion']);
        });
    }
}