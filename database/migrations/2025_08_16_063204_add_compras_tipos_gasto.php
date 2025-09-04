<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('dim_tipo_gasto')->insert([
            ['nombre' => 'COMPRAS BODEGA'],
            ['nombre' => 'COMPRAS HOTEL'],
        ]);
    }

    public function down(): void
    {
        DB::table('dim_tipo_gasto')
            ->whereIn('nombre', ['COMPRAS BODEGA', 'COMPRAS HOTEL'])
            ->delete();
    }
};