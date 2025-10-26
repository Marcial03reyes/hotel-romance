<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNacionalidadToDimRegistroClientesTable extends Migration
{
    public function up()
    {
        Schema::table('dim_registro_clientes', function (Blueprint $table) {
            $table->string('nacionalidad', 50)->nullable()->after('lugar_nacimiento');
        });
    }

    public function down()
    {
        Schema::table('dim_registro_clientes', function (Blueprint $table) {
            $table->dropColumn('nacionalidad');
        });
    }
}