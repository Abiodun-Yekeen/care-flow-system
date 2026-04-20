<?php

use App\Modules\Admin\Http\Controllers\Web\AdminOverviewController;
use App\Modules\Core\Iam\Http\Controllers\Web\PolicyController;
use App\Modules\Core\Iam\Http\Controllers\Web\RoleController;
use App\Modules\Core\Iam\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', [AdminOverviewController::class, 'index'])->name('overview.admin');
    Route::get('users/import-data', [UserController::class, 'showImport'])->name('users.import-user-data');
    Route::post('users/import-data', [UserController::class, 'uploadUserData'])->name('users.import-user-data');
    Route::post('/users/{user}/roles', [UserController::class, 'assignRoles'])->name('users.roles.assign');
    Route::delete('/users/{user}/roles/{role}', [UserController::class, 'removeRole'])->name('users.roles.remove');
    Route::get('/users/{user}/effective-access', [UserController::class, 'effectiveAccess'])->name('users.effective-access');
    Route::post('/roles/{role}/policies', [RoleController::class, 'attachPolicies'])->name('roles.policies.attach');
    Route::delete('/roles/{role}/policies/{policy}', [RoleController::class, 'detachPolicy'])->name('roles.policies.detach');
    Route::post('/roles/{role}/parents', [RoleController::class, 'attachParents'])->name('roles.parents.attach');
    Route::delete('/roles/{role}/parents/{parent}', [RoleController::class, 'detachParent'])->name('roles.parents.detach');
    Route::get('/roles/{role}/effective-access', [RoleController::class, 'effectiveAccess'])->name('roles.effective-access');
    Route::post('/policies/{policy}/simulate', [PolicyController::class, 'simulate'])->name('policies.simulate');
    Route::resource('users',UserController::class);
    Route::resource('roles',RoleController::class);
    Route::resource('policies',PolicyController::class);



});


