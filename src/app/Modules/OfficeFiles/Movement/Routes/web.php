<?php


use App\Modules\OfficeFiles\Movement\Http\Controllers\Web\MovementController;
use App\Modules\OfficeFiles\Movement\Http\Controllers\Web\MovementOverviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('tracking')->group(function () {
    Route::get('/', [MovementOverviewController::class, 'index'])->name('overview.file-movement');
    Route::resource('file-movement', MovementController::class);

});




