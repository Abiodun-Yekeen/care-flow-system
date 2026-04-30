<?php

namespace App\Modules\OfficeFiles\File\Routes;

use App\Modules\OfficeFiles\File\Http\Controllers\Web\FileController;
use App\Modules\OfficeFiles\File\Http\Controllers\Web\FileOverviewController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['web','auth'])->prefix('my-desk')->group(function () {
    Route::get('/', [FileOverviewController::class, 'index'])->name('overview.my-desk');
   Route::put('files/treat-file/{file}', [FileController::class, 'treatFile'])->name('files.treat');
   Route::post('files/search-merge-file',[FileController::class, 'searchFileToMerger'])->name('files.search-merge-file');
   Route::resource('files', FileController::class);

});

