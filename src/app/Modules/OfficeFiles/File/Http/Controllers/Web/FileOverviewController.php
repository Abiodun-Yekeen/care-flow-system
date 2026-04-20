<?php

namespace App\Modules\OfficeFiles\File\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class FileOverviewController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('modules/overview/ModuleOverview', ['title' => 'My Desk']);
    }
}
