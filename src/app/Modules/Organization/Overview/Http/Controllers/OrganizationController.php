<?php

namespace App\Modules\Organization\Overview\Http\Controllers;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class OrganizationController extends Controller
{

    public function index()
    {
        return Inertia::render('modules/overview/ModuleOverview', []);
    }
}
