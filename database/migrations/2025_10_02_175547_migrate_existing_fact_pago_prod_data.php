<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migra los datos existentes de fact_pago_prod con fecha y turno desde fact_registro_clientes
     */
    public function up(): void
    {
        // Actualizar registros existentes con fecha_venta y turno desde fact_registro_clientes
        DB::statement("
            UPDATE fact_pago_prod fp
            INNER JOIN fact_registro_clientes fr ON fp.id_estadia = fr.id_estadia
            SET 
                fp.fecha_venta = fr.fecha_ingreso,
                fp.turno = COALESCE(fr.turno, 0)
            WHERE fp.id_estadia IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se puede revertir la migración de datos de forma segura
        // Los campos fecha_venta y turno se eliminarían en la migración anterior
    }
};