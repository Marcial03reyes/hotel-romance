<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDatosEstadiaToFactRegistroClientesTable extends Migration
{
    public function up()
    {
        Schema::table('fact_registro_clientes', function (Blueprint $table) {
            $table->string('ciudad_procedencia', 100)->nullable()->after('obs');
            $table->string('ciudad_destino', 100)->nullable()->after('ciudad_procedencia');
            $table->string('motivo_viaje', 100)->nullable()->after('ciudad_destino');
            $table->string('placa_vehiculo', 20)->nullable()->after('motivo_viaje');
        });
    }

    public function down()
    {
        Schema::table('fact_registro_clientes', function (Blueprint $table) {
            $table->dropColumn([
                'ciudad_procedencia',
                'ciudad_destino', 
                'motivo_viaje',
                'placa_vehiculo'
            ]);
        });
    }
}