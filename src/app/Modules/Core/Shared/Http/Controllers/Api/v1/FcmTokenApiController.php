<?php

namespace App\Modules\Core\Shared\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FcmTokenApiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $user->fcmTokens()->updateOrCreate(
            ['token' => $request->token],
            []
        );

        return response()->json(['success' => true]);
    }
}
