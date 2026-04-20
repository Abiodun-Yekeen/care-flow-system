<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



// Create partitions daily to ensure we are always ahead
Schedule::command('audit:create-partition')->daily();

// Retention can stay yearly or monthly
Schedule::command('audit:retention')->monthly();

