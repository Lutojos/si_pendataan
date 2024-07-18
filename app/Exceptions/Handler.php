<?php

/**
 *
 */

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Handler.
 */
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
        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return _401('No Authorization Header Value');
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->ajax() || $request->expectsJson()) {
            $message     = config("app.debug") ? $exception->getFile() . " line " . $exception->getLine() . " " . $exception->getMessage() : "Internal Server Error";
            $status_code = 500;

            if ($exception instanceof HttpExceptionInterface) {
                $status_code = $exception->getStatusCode();
                $message     = $exception->getMessage(); // on HTTPException, just return a normal message without path & line
            }

            if ($exception instanceof ValidationException) {
                $status_code = $exception->status;
                $message     = $exception->validator->errors();
            }
            if ($exception instanceof ThrottleRequestsException) {
                $status_code = 429;
                //$message = 'Akun anda telah terblokir karena melakukan percobaan login Sebanyak 3x. Silahkan coba lagi dalam :seconds detik';
                $message = __('auth.throttle', ['seconds' => $exception->getHeaders()['Retry-After']]);
            }

            if ($exception instanceof TokenMismatchException) {
                $status_code = 419;
                $message     = $exception->getMessage();
                $response    = jsonError($message, $status_code);

                return response()->json(array_merge((array) $response->getData(), [
                    'error'    => $message,
                    'redirect' => url('/'),
                ]), 419);
            }

            return jsonError($message, $status_code);
        }

        return parent::render($request, $exception);
    }
}
