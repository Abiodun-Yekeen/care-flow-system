<?php

namespace App\Modules\Admin\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AdminOverviewController extends Controller
{

    public function index()
    {
        return Inertia::render('modules/overview/ModuleOverview', []);
    }
}
