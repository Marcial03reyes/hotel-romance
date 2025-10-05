<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Verificar si el índice existe antes de eliminarlo
        $indexExists = DB::select("SHOW INDEX FROM fact_pagos_gastos_fijos WHERE Key_name = 'unique_pago_fijo_mes_anio'");
        
        if (!empty($indexExists)) {
            // 1. Eliminar clave foránea temporalmente
            Schema::table('fact_pagos_gastos_fijos', function (Blueprint $table) {
                $table->dropForeign(['id_gasto_fijo']);
            });
            
            // 2. Eliminar índice único
            DB::statement('ALTER TABLE fact_pagos_gastos_fijos DROP INDEX unique_pago_fijo_mes_anio');
            
            // 3. Eliminar columnas
            Schema::table('fact_pagos_gastos_fijos', function (Blueprint $table) {
                $table->dropColumn(['mes', 'anio', 'turno']);
            });
            
            // 4. Recrear clave foránea
            Schema::table('fact_pagos_gastos_fijos', function (Blueprint $table) {
                $table->foreign('id_gasto_fijo')
                      ->references('id_gasto_fijo')
                      ->on('fact_gastos_fijos')
                      ->onDelete('cascade');
            });
        } else {
            // Si el índice no existe, solo eliminar columnas si existen
            if (Schema::hasColumn('fact_pagos_gastos_fijos', 'mes')) {
                Schema::table('fact_pagos_gastos_fijos', function (Blueprint $table) {
                    $table->dropColumn('mes');
                });
            }
            
            if (Schema::hasColumn('fact_pagos_gastos_fijos', 'anio')) {
                Schema::table('fact_pagos_gastos_fijos', function (Blueprint $table) {
                    $table->dropColumn('anio');
                });
            }
            
            if (Schema::hasColumn('fact_pagos_gastos_fijos', 'turno')) {
                Schema::table('fact_pagos_gastos_fijos', function (Blueprint $table) {
                    $table->dropColumn('turno');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('fact_pagos_gastos_fijos', function (Blueprint $table) {
            // Verificar si la clave foránea existe antes de eliminarla
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = 'fact_pagos_gastos_fijos' 
                AND COLUMN_NAME = 'id_gasto_fijo' 
                AND CONSTRAINT_NAME LIKE '%foreign%'
            ");
            
            if (!empty($foreignKeys)) {
                $table->dropForeign(['id_gasto_fijo']);
            }
            
            // Restaurar columnas solo si no existen
            if (!Schema::hasColumn('fact_pagos_gastos_fijos', 'mes')) {
                $table->integer('mes')->after('id_gasto_fijo')->nullable();
            }
            
            if (!Schema::hasColumn('fact_pagos_gastos_fijos', 'anio')) {
                $table->integer('anio')->after('mes')->nullable();
            }
            
            if (!Schema::hasColumn('fact_pagos_gastos_fijos', 'turno')) {
                $table->tinyInteger('turno')->default(0)->after('monto_pagado')->nullable();
            }
        });
        
        // Verificar si el índice existe antes de recrearlo
        $indexExists = DB::select("SHOW INDEX FROM fact_pagos_gastos_fijos WHERE Key_name = 'unique_pago_fijo_mes_anio'");
        
        if (empty($indexExists)) {
            Schema::table('fact_pagos_gastos_fijos', function (Blueprint $table) {
                $table->unique(['id_gasto_fijo', 'mes', 'anio'], 'unique_pago_fijo_mes_anio');
            });
        }
        
        // Recrear clave foránea
        Schema::table('fact_pagos_gastos_fijos', function (Blueprint $table) {
            $table->foreign('id_gasto_fijo')
                  ->references('id_gasto_fijo')
                  ->on('fact_gastos_fijos')
                  ->onDelete('cascade');
        });
    }
};