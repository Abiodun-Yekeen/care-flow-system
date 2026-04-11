<?php

namespace App\Modules\Core\Shared\Traits;

trait HasResponse
{
    public function success($message, $data = null, $code = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "success" => true,
            "message" => $message,
            "data" => $data
        ], $code);
    }

    public function error($message, $errors = null, $code = 500): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "success" => false,
            "message" => $message,
            "errors" => $errors
        ], $code);
    }
}
