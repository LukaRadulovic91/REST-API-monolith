<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Throwable;

/**
 * Class Handler
 *
 * @package App\Exceptions
 */
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

        $this->renderable(function (\App\Exceptions\Domain\NotFoundException $e, $request) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (\App\Exceptions\Domain\ValidationException $e, $request) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $this->renderable(function (\Throwable $e, $request) {
            \Log::error($e);

            return response()->json([
                'success' => false,
                'error' => 'Something went wrong. Please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }
}
