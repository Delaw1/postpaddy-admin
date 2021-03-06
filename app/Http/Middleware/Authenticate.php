<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use \App\User;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    // protected $redirectTo = '/unauthorized';
    protected function redirectTo($request)
    {
        // if (! $request->expectsJson()) {
        //     return route('unauthorized'); 
        // }
        return route('unauthorized'); 
        // return 'http://postpaddy.com/api/unauthorized';
    }
}
