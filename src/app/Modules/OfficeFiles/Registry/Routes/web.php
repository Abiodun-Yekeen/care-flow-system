<?php


use App\Modules\OfficeFiles\Registry\Http\Controllers\Web\RegistryController;
use App\Modules\OfficeFiles\Registry\Http\Controllers\Web\RegistryOverviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('registry')->group(function () {
    Route::get('/', [RegistryOverviewController::class, 'index'])->name('overview.registry');
    Route::get('register', [RegistryController::class, 'create'])->name('create.registry');
    Route::get('temp-files', [RegistryController::class, 'temporaryFile'])->name('temp-file.registry');


});




