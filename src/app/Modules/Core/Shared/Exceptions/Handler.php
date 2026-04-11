<?php

namespace App\Modules\Core\Shared\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Handle CSRF token mismatch (419 error)
        if ($exception instanceof TokenMismatchException) {
            return $this->handleTokenMismatch($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Handle token mismatch exception gracefully.
     */
    protected function handleTokenMismatch(Request $request, Throwable $exception): Response
    {
        // For API/JSON requests
        if ($request->expectsJson() && !$request->header('X-Inertia')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session expired.',
            ], 419);
        }

        // For Inertia.js requests
        if ($request->header('X-Inertia')) {
            return response()->json([
                'message' => 'Session expired'
            ], 419)->header('X-Inertia-Location', url()->current());
        }

        // For regular web requests - redirect to login or back
        if ($request->is('login') || $request->is('register')) {
            return redirect()->route('login')
                ->withInput($request->except('_token'))
                ->with('error', 'Session expired. Please try again.');
        }

        // For forms, go back with data preserved
        return redirect()->back()
            ->withInput($request->except('_token'))
            ->with([
                'error' => 'Your session has expired. Please try again.',
                'show_modal' => true,
                'expired' => true
            ]);
    }
}
