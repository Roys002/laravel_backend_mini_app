<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;

use Throwable;
use App\Helpers\ApiResponse;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            // 404 Not Found 
            if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
                return ApiResponse::error('Resource not found', 404);
            }
            // Unauthenticated
            if ($e instanceof AuthenticationException) {
                return ApiResponse::error('Unauthenticated', 401);
            }
            // Default (500 atau lain-lain)
            return ApiResponse::error(
                config('app.debug') ? $e->getMessage() : 'Server Error',
                $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500
            );
        }

        // Kalau bukan request API → pakai bawaan Laravel (HTML error page)
        return parent::render($request, $e);
    }

    // protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    // {
    //     return response()->json(['message' => 'Unauthenticated'], 401);
    // }
}
