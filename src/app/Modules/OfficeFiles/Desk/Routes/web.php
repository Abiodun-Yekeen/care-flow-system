<?php

use App\Modules\OfficeFiles\MyDesk\Http\Controllers\Web\MyDeskOverviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('workdesk')->group(function () {
 Route::get('/', [MyDeskOverviewController::class, 'index'])->name('desk');
});




