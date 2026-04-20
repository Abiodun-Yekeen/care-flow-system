<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateAuditPartition extends Command
{
    protected $signature = 'audit:create-partition';

    public function handle()
    {
        // Check both This Month and Next Month
        $months = collect(range(0, 3))->map(fn ($i) => now()->addMonths($i));

        foreach ($months as $date) {
            $start = $date->copy()->startOfMonth()->toDateTimeString();
            $end = $date->copy()->addMonth()->startOfMonth()->toDateTimeString();
            $tableName = 'audit_logs_' . $date->format('Y_m');

            DB::statement("
        CREATE TABLE IF NOT EXISTS {$tableName}
        PARTITION OF audit_logs
        FOR VALUES FROM ('{$start}') TO ('{$end}');
    ");
        }
    }
}
