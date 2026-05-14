<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render(Request $request, Throwable $exception): Response
    {
        // In production, never leak stack traces or internal details
        if (config('app.debug') === false && !$this->isHttpException($exception)) {
            // Log the error for internal debugging
            Log::error('Application exception', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'url' => $request->url(),
                'method' => $request->method(),
                'user_id' => auth()->id(),
            ]);

            // Return generic error page without exposing details
            return response()->view('errors.500', [], 500);
        }

        return parent::render($request, $exception);
    }

    /**
     * Check if exception is an HTTP exception
     */
    protected function isHttpException(Throwable $e): bool
    {
        return $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException;
    }
}
