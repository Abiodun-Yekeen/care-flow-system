<?php

namespace App\Modules\OfficeFiles\File\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class FileController
{

    public function index()
    {
        return view('index');
    }
}
