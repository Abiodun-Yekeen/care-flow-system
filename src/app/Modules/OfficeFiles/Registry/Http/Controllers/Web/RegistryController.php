<?php

namespace App\Modules\OfficeFiles\Registry\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class RegistryController extends Controller
{
    public function index()
    {
        return Inertia::render('modules/registry/pages/Index', []);
    }

    public function create()
    {
        return Inertia::render('modules/registry/pages/Create', []);
    }

    public function temporaryFile()
    {
        return Inertia::render('modules/registry/pages/Index', []);
    }

}
