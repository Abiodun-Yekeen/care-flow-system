<?php
if (!function_exists('audit')) {
    function audit(): \App\Modules\Core\Audit\Services\AuditService
    {
        return app(\App\Modules\Core\Audit\Services\AuditService::class);
    }
}
