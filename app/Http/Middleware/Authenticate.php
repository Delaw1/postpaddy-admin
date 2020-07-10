<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use \App\User;
use Closure;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next)
    {
        $userID = $request->input("user_id");
        
        if($userID != NULL && User::find($userID) != NULL){
            Auth::loginUsingId($userID);
        }

        return $next($request);  
    }
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
