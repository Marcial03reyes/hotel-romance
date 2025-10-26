<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Extender el ENUM para incluir 'CONSUMO INTERNO'
        DB::statement("ALTER TABLE dim_met_pago MODIFY COLUMN met_pago ENUM('Efectivo','Plin','Tarjeta crédito','Transferencia','Yape','CONSUMO INTERNO')");
        
        // 2. Verificar si ya existe el registro (para evitar duplicados)
        $exists = DB::table('dim_met_pago')->where('met_pago', 'CONSUMO INTERNO')->exists();
        
        // 3. Insertar solo si no existe
        if (!$exists) {
            DB::table('dim_met_pago')->insert([
                'id_met_pago' => 99,  // ID alto para evitar conflictos
                'met_pago' => 'CONSUMO INTERNO'
            ]);
        }
    }

    public function down()
    {
        // 1. Eliminar el registro
        DB::table('dim_met_pago')->where('id_met_pago', 99)->delete();
        
        // 2. Restaurar ENUM original
        DB::statement("ALTER TABLE dim_met_pago MODIFY COLUMN met_pago ENUM('Efectivo','Plin','Tarjeta crédito','Transferencia','Yape')");
    }
};