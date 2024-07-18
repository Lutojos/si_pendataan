<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MobileApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-API-KEY');

        if (!$key) {
            return jsonError('X-API-KEY tidak ditemukan', 400);
        }

        $allkey = explode(',', config('app.api_key'));

        if (!in_array($key, $allkey)) {
            return jsonError('Mobile API Key tidak sah.', 422);
        }

        return $next($request);
    }
}
