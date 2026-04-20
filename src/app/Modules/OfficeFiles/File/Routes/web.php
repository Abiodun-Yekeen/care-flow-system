<?php

namespace App\Modules\OfficeFiles\File\Routes;

use App\Modules\OfficeFiles\File\Http\Controllers\Web\FileController;
use App\Modules\OfficeFiles\File\Http\Controllers\Web\FileOverviewController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['web','auth'])->prefix('my-desk')->group(function () {
    Route::get('/', [FileOverviewController::class, 'index'])->name('overview.my-desk');
  Route::resource('files', FileController::class);

});

