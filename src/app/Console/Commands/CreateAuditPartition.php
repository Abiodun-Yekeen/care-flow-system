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
        $nextMonth = Carbon::now()->addMonth();
        $start = $nextMonth->startOfMonth()->format('Y-m-d');
        $end = $nextMonth->endOfMonth()->addDay()->format('Y-m-d');

        $tableName = 'audit_logs_' . $nextMonth->format('Y_m');

        DB::statement("
            CREATE TABLE IF NOT EXISTS {$tableName}
            PARTITION OF audit_logs
            FOR VALUES FROM ('{$start}') TO ('{$end}');
        ");
    }
}
