<?php


use App\Modules\OfficeFiles\Movement\Http\Controllers\Web\MovementController;
use App\Modules\OfficeFiles\Movement\Http\Controllers\Web\MovementOverviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('tracking', [MovementOverviewController::class, 'index'])->name('overview.file-movement');
    Route::get('my-desk/history', [MovementController::class, 'index']);

});




