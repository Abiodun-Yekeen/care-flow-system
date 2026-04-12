<?php

namespace App\Modules\Core\Audit\Services;

use App\Modules\Core\Audit\Jobs\StoreAuditLogJob;
use Illuminate\Support\Str;

class AuditService
{
    public function log(array $data): void
    {
        $payload = [
            'event_id' => (string) Str::uuid(),
            'category' => $data['category'],
            'action' => $data['action'],
            'resource_type' => $data['resource_type'] ?? null,
            'resource_id' => $data['resource_id'] ?? null,
            'user_id' => auth()->id() ?? $data['user_id'] ?? null,
            'user_email' => auth()->id() ?? $data['email'] ?? null,
            'changes' => json_encode($data['changes'] ?? null),
            'metadata' => json_encode($data['metadata'] ?? null),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'occurred_at' => now()->toDateTimeString(),
            'created_at' => now(),
            'updated_at' => now(),
        ];


        StoreAuditLogJob::dispatch($payload)
            ->onConnection('audit')
            ->onQueue('audit');
            }
}
