<?php

use App\Modules\Organization\Department\Http\Controllers\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('org')->group(function () {
Route::get('/departments/{department}/staff', [DepartmentController::class, 'getStaff'])
    ->name('departments.staff');

});
