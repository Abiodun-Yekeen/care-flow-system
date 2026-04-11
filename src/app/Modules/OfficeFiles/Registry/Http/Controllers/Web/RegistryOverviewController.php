<?php

namespace App\Modules\OfficeFiles\Registry\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class RegistryOverviewController extends Controller
{
        public function index()
        {
            return Inertia::render('modules/overview/ModuleOverview', []);
        }

}
