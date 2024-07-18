<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="SAMBINA",
 *      @OA\License(name="MIT"),
 *      @OA\Attachable()
 * ),
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST
 * ),
 * @OA\SecurityScheme(
 *      securityScheme="token",
 *      in="header",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * )
 *  @OA\SecurityScheme(
 *      securityScheme="mobilekey",
 *      in="header",
 *      type="apiKey",
 *      name="X-API-KEY",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey($path = 'login')
    {
        return Str::lower(request('email')) . '|' . request()->ip() . '|' . $path;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     */
    public function checkTooManyFailedAttempts($path = 'login')
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($path), 3)) {
            return;
        }
        $seconds = RateLimiter::availableIn($this->throttleKey($path));
        throw new \Exception('Terlalu banyak percobaan. bisa mencoba kembali dalam ' . $seconds . ' detik.');
    }
}
