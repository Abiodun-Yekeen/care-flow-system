<?php

namespace App\Modules\Dashboard\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{

    public function index(): Response
    {
        return Inertia::render('modules/dashboard/pages/index',[]);



    }
}
