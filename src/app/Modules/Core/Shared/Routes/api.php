<?php

namespace App\Modules\Core\Shared\Routes;

use App\Modules\Core\Shared\Http\Controllers\Api\v1\FcmTokenApiController;
use App\Modules\Core\Shared\Http\Controllers\Api\v1\MetaDataApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['web','auth'])->group(function () {

   Route::get('load-metadata',[MetaDataApiController::class,'loadMetaData'])->name('api.load-metadata');
   Route::get('refresh-session',[MetaDataApiController::class,'keepSessionAlive'])->name('api.refresh-session');
    Route::any('fcm-token',[FcmTokenApiController::class,'store'])->name('api.fcm-token');

   Route::get('notifications', function () {
        return auth()->user()->notifications()->limit(20)->get();
    });

    // Mark single notification as read
    Route::patch('/notifications/{id}/read', function ($id) {
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
        return response()->json(['status' => 'success']);
    });

    // Mark all as read
    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['status' => 'success']);
    });

});
