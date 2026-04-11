<?php

namespace App\Modules\Core\Shared\Routes;

use App\Modules\Core\Shared\Http\Controllers\Api\v1\MetaDataApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('web')->group(function () {

   Route::get('load-metadata',[MetaDataApiController::class,'loadMetaData'])->name('api.load-metadata');
   Route::get('refresh-session',[MetaDataApiController::class,'keepSessionAlive'])->name('api.refresh-session');

});
