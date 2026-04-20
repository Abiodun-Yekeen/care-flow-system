<?php

use App\Modules\OfficeFiles\Report\Http\Controllers\Web\ReportOverviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
 Route::get('/reports', [ReportOverviewController::class, 'index'])->name('offices');
});




