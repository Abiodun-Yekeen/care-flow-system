<?php


use App\Modules\OfficeFiles\Document\Http\Controllers\Web\DocumentController;

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('my-desk/documents', [DocumentController::class, 'index']);

});