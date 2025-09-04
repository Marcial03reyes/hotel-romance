<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DimTipoGastoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('dim_tipo_gasto')->truncate(); // si usas MySQL con FK, quita esta línea
        DB::table('dim_tipo_gasto')->insert([
            ['id_tipo_gasto' => 1, 'nombre' => 'Salario'],
            ['id_tipo_gasto' => 2, 'nombre' => 'Servicios'],
            ['id_tipo_gasto' => 3, 'nombre' => 'Mantenimiento'],
            ['id_tipo_gasto' => 4, 'nombre' => 'Suscripciones'],
        ]);
    }
}