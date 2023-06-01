<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Exceptions\InvalidOrderException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
     */
    public function register(): void
    {
        // $this->reportable(function (Throwable $e) {

        // });

        $this->renderable(function (Throwable $e, Request $request) {
            // DEBUG 모드가 아닐 때만
            if (!env('APP_DEBUG')) {
                $responseError = json_encode([
                    'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'SERVER ERROR'
                ], true);
                print_r($responseError);
                return response()->view('error',[],Response::HTTP_INTERNAL_SERVER_ERROR)->header('Content-Type', 'application/json');
            }
        });
    }
}
