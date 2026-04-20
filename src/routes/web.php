<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Shared\Services\Firebase\FcmService;
use App\Modules\Dashboard\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return redirect()->route('login');
    });


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    Route::get('/test-fcm', function () {

        $user = User::find(1);

       // $tokens = $this->user->fcmTokens->pluck('token')->toArray();
//
//        $fcm->sendMulticast(
//            $tokens,
//            $this->title,
//            $this->body,
//            $this->data
//        );

        foreach ($user->fcmTokens as $token) {
            app(FcmService::class)->send(
                $token->token,
                "Test Notification",
                "This is a test push",
                ['test' => true]
            );
        }
//


        return "sent";
    });


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';
