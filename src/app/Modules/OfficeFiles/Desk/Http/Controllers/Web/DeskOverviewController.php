<?php

namespace App\Modules\OfficeFiles\Desk\Http\Controllers\Web;

use Inertia\Inertia;

class DeskOverviewController
{

    public function index()
    {
        return Inertia::render('modules/overview/ModuleOverview',[]);
    }
}
