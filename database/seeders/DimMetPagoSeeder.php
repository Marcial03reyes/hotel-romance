<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DimMetPagoSeeder extends Seeder
{
    public function run(): void
    {
        // Antes: DB::table('dim_met_pago')->truncate();
        DB::table('dim_met_pago')->delete();
        DB::table('dim_met_pago')->insert([
            ['id_met_pago' => 1, 'met_pago' => 'Efectivo'],
            ['id_met_pago' => 2, 'met_pago' => 'Yape'],
            ['id_met_pago' => 3, 'met_pago' => 'Plin'],
            ['id_met_pago' => 4, 'met_pago' => 'Tarjeta'],
            ['id_met_pago' => 5, 'met_pago' => 'QR'],
        ]);
    }
}