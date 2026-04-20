<?php


use App\Modules\Organization\Overview\Http\Controllers\OrganizationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('org')->group(function () {
    Route::get('/', [OrganizationController::class, 'index'])->name('overview.org');

});




