<?php

namespace App\Modules\OfficeFiles\Movement\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class MovementOverviewController extends Controller
{
    public function index()
    {
        return Inertia::render('modules/overview/ModuleOverview', []);
    }

}
