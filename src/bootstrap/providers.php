<?php

return [
    App\Modules\Core\Iam\Providers\IamAuthorizationProvider::class,
    App\Providers\HorizonServiceProvider::class,
    \App\Modules\Core\Shared\Http\Middleware\AppMiddleware::class,
    \App\Modules\Core\Shared\Providers\AppProvider::class,
    \App\Modules\Clinical\Patient\Providers\PatientProvider::class,
];
