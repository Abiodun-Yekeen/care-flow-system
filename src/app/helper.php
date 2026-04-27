<?php

use App\Modules\Core\Iam\Services\IamAuthorizationService;

if (!function_exists('audit')) {
    function audit(): \App\Modules\Core\Audit\Services\AuditService
    {
        return app(\App\Modules\Core\Audit\Services\AuditService::class);
    }
}

 function invalidateRoleUsers($role): void
{
    $users = $role->users()->pluck('id');

    foreach ($users as $userId) {
        app(IamAuthorizationService::class)
            ->forgetUserSnapshot($userId);
    }
}
