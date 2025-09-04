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
        DB::statement("ALTER TABLE fact_registro_clientes MODIFY habitacion ENUM('201','202','203','204','205','206','207','208','209','210','301','302','303','304','305','306','307','308','309','310','401','402','403','404','405','406','407','408','409','410')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fact_registro_clientes', function (Blueprint $table) {
            //
        });
    }
};
