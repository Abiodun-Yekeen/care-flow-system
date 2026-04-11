<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditRetention extends Command
{
    protected $signature = 'audit:retention';

    public function handle()
    {
        $cutoff = Carbon::now()->subYears(10);

        $oldPartitions = DB::select("
            SELECT tablename
            FROM pg_tables
            WHERE tablename LIKE 'audit_logs_%'
        ");

        foreach ($oldPartitions as $table) {
            if (preg_match('/audit_logs_(\d{4})_(\d{2})/', $table->tablename, $matches)) {
                $year = $matches[1];
                $month = $matches[2];

                $partitionDate = Carbon::create($year, $month, 1);

                if ($partitionDate->lt($cutoff)) {
                    $filename = storage_path("audit_backups/{$table->tablename}.sql");

                    exec("pg_dump -U postgres -t {$table->tablename} emr > {$filename}");

                    DB::statement("DROP TABLE {$table->tablename}");

                }
            }
        }
    }
}
