<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ListDbColumns extends Command
{
    protected $signature = 'db:columns {--show-types}';
    protected $description = 'Lista todas las tablas y sus columnas (opcional: tipos).';

    public function handle()
    {
        $db = DB::connection()->getDatabaseName();
        $this->info("Base de datos: {$db}");

        $tables = DB::select(
            "SELECT TABLE_NAME AS t FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME",
            [$db]
        );

        foreach ($tables as $row) {
            $table = $row->t;
            $this->line(PHP_EOL."=== TABLA: {$table} ===");

            $cols = Schema::getColumnListing($table);
            if (!$this->option('show-types')) {
                foreach ($cols as $c) {
                    $this->line("  • {$c}");
                }
                continue;
            }

            // Con tipos (sin necesidad de doctrine/dbal)
            $meta = DB::select(
                "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, COLUMN_KEY
                 FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
                 ORDER BY ORDINAL_POSITION",
                [$db, $table]
            );
            foreach ($meta as $m) {
                $this->line(sprintf(
                    "  • %-30s  %-20s  NULL:%-3s  DEF:%s  KEY:%s",
                    $m->COLUMN_NAME, $m->COLUMN_TYPE, $m->IS_NULLABLE, var_export($m->COLUMN_DEFAULT, true), $m->COLUMN_KEY
                ));
            }
        }

        return self::SUCCESS;
    }
}
