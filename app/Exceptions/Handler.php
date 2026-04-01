<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // keep default reporting
        });

        $this->renderable(function (TokenMismatchException $e, $request) {
            Log::warning('CSRF token mismatch (419)', [
                'path' => $request->path(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'origin' => $request->getSchemeAndHttpHost(),
                'user_id' => optional($request->user())->id,
                'has_token' => $request->has('_token'),
            ]);
            // Let Laravel render the default 419 response
        });
    }
}
