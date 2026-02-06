<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
        //
    }

    public function render($request, Throwable $e)
    {
        // Log the exception
        Log::error($e);

        $message = $e->getMessage();
        $trace = config('app.debug') ? $e->getTraceAsString() : null;

        if (View::exists('errors.500')) {
            return response()->view('errors.500', ['message' => $message, 'trace' => $trace], 500);
        }

        // Fallback
        return parent::render($request, $e);
    }
}
