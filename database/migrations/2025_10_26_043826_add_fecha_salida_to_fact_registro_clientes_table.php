<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaSalidaToFactRegistroClientesTable extends Migration
{
    public function up()
    {
        Schema::table('fact_registro_clientes', function (Blueprint $table) {
            $table->date('fecha_salida')->nullable()->after('hora_salida');
        });
    }

    public function down()
    {
        Schema::table('fact_registro_clientes', function (Blueprint $table) {
            $table->dropColumn('fecha_salida');
        });
    }
}