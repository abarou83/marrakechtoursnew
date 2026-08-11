<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ImportSqliteToMysql extends Command
{
    protected $signature = 'db:import-sqlite
                            {--fresh : Recréer les tables MySQL avant import (migrate:fresh)}';

    protected $description = 'Copie les données SQLite (database/database.sqlite) vers MySQL';

    public function handle(): int
    {
        $sqlitePath = config('database.connections.sqlite_legacy.database');
        if (! is_string($sqlitePath) || ! is_file($sqlitePath)) {
            $this->error("Fichier SQLite introuvable : {$sqlitePath}");

            return self::FAILURE;
        }

        if (config('database.default') !== 'mysql') {
            $this->error('DB_CONNECTION doit être mysql dans .env');

            return self::FAILURE;
        }

        try {
            DB::connection('sqlite_legacy')->getPdo();
        } catch (Throwable $e) {
            $this->error('Connexion SQLite : '.$e->getMessage());

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->call('migrate:fresh', ['--force' => true]);
        } elseif (! Schema::connection('mysql')->hasTable('migrations')) {
            $this->info('Migration du schéma MySQL…');
            $this->call('migrate', ['--force' => true]);
        }

        $sqliteTables = collect(DB::connection('sqlite_legacy')->select(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'"
        ))->pluck('name');

        $mysqlTables = collect(DB::connection('mysql')->select('SHOW TABLES'))
            ->map(fn ($row) => array_values((array) $row)[0]);

        $tables = $sqliteTables->intersect($mysqlTables)->values();

        if ($tables->isEmpty()) {
            $this->error('Aucune table commune entre SQLite et MySQL.');

            return self::FAILURE;
        }

        $this->info('Import de '.$tables->count().' table(s)…');

        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

        $bar = $this->output->createProgressBar($tables->count());
        $bar->start();

        foreach ($tables as $table) {
            try {
                DB::connection('mysql')->table($table)->truncate();

                $rows = DB::connection('sqlite_legacy')->table($table)->get();
                foreach ($rows->chunk(200) as $chunk) {
                    $payload = $chunk->map(fn ($row) => (array) $row)->all();
                    if ($payload !== []) {
                        DB::connection('mysql')->table($table)->insert($payload);
                    }
                }
            } catch (Throwable $e) {
                $this->newLine();
                $this->warn("Table {$table} : ".$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Import terminé. Base : '.config('database.connections.mysql.database'));

        return self::SUCCESS;
    }
}
