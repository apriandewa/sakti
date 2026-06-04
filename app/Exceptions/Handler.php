<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
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
        $this->renderable(function (ThrottleRequestsException $e, $request) {

            // ambil sisa waktu dalam detik
            $retryAfterSeconds = (int) ($e->getHeaders()['Retry-After'] ?? 0);

            // konversi ke menit (dibulatkan ke atas)
            $retryAfterMinutes = max(10, ceil($retryAfterSeconds / 60));

            return response()->view('errors.429', [
                'data' => [
                    'title'   => 'Terlalu Banyak Percobaan Login',
                    'code'    => 429,
                    'message' => "Akun Anda dikunci selama {$retryAfterMinutes} menit karena terlalu banyak percobaan login yang gagal.",
                    'retry_after_seconds' => $retryAfterSeconds,
                    'retry_after_minutes' => $retryAfterMinutes,
                ]
            ], 429);
        });

        /**
         * ⛔ 403 - Forbidden (pesan dari controller / abort)
         */
        $this->renderable(function (HttpException $e, $request) {

            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', [
                    'data' => [
                        'title'   => 'Akses Ditolak',
                        'code'    => 403,
                        'message' => $e->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.',
                    ]
                ], 403);
            }
        });
    }
}
