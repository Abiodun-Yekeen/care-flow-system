<?php

namespace App\Modules\OfficeFiles\Report\Http\Controllers\Web;

use Inertia\Inertia;

class ReportOverviewController
{

    public function index()
    {
        return Inertia::render('modules/overview/ModuleOverview',[]);
    }
}
