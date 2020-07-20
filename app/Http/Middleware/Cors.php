<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;

class Cors
{
    public function handle($request, Closure $next)
    {
        $conditions = array(
            'email' => 'lawrenceajayi481@gmail.com',
            'password' => '12345678'
        );
        /* check if user credentials is okay */
        
        // Auth::attempt($conditions);
        Auth::loginUsingId(14);
        return $next($request);
    }
}
