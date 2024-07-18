<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XssSanitizerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$input) {
            $input = htmlentities($input);
        });

        $request->merge($input);

        return $next($request);
    }
}
