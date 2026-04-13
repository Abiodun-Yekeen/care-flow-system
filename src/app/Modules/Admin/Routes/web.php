<?php

use App\Modules\Admin\Http\Controllers\Web\AdminOverviewController;
use App\Modules\Core\Iam\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', [AdminOverviewController::class, 'index'])->name('overview.admin');
    Route::get('users/import-data', [UserController::class, 'showImport'])->name('users.import-user-data');
    Route::post('users/import-data', [UserController::class, 'uploadUserData'])->name('users.import-user-data');
    Route::resource('users',UserController::class);



});


