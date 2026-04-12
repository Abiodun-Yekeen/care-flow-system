<?php

namespace App\Modules\Core\Audit\Jobs;


use App\Modules\Core\Audit\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StoreAuditLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 5;
    public $timeout = 30;

    public function backoff(): array
    {
        return [5, 15, 30, 60];
    }

    public function __construct(public array $payload) {}

    public function handle(): void
    {
        AuditLog::create([
            ...$this->payload,
            'changes'  => is_array($this->payload['changes'] ?? null)
                ? json_encode($this->payload['changes'])
                : $this->payload['changes'],
            'metadata' => is_array($this->payload['metadata'] ?? null)
                ? json_encode($this->payload['metadata'])
                : $this->payload['metadata'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('Audit job failed', [
            'payload' => $this->payload,
            'error'   => $e->getMessage(),
        ]);
    }
}
