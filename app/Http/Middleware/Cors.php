<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    public function handle($request, Closure $next)
    {
        $allowedOrigins = ['http://localhost:3000', 'https://postslate.com'];
        $origin = $request->server('HTTP_ORIGIN');

        if (in_array($origin, $allowedOrigins)) {
            return $next($request)->header('Access-Control-Allow-Origin', $origin)->header('Access-Control-Allow-Credentials', true)->header('Access-Control-Allow-Methods', '*')->header('Access-Control-Allow-Headers', 'Origin, Content-Type, X-Auth-Token, Authorization, X-Requested-With, x-xsrf-token');
        }

        return $next($request);
    }
}
