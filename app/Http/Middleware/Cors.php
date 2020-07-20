<?php
namespace App\Http\Middleware;
use Closure;

class Cors
{
  public function handle($request, Closure $next)
  {
    // $allowedOrigins = ['example.com', 'example1.com', 'example2.com'];
    // $origin = $_SERVER['HTTP_ORIGIN'];

    // if (in_array($origin, $allowedOrigins)) {
    //     return $next($request)
    //         ->header('Access-Control-Allow-Origin', $origin)
    //         ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE')
    //         ->header('Access-Control-Allow-Headers', 'Content-Type');
    // }

    // return $next($request);
    return $next($request)
      ->header('Access-Control-Allow-Origin', 'http://localhost:3000')
      ->header('Access-Control-Allow-Credentials', true)
      ->header('Access-Control-Allow-Methods', '*')
      ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, X-Auth-Token, Authorization, X-Requested-With, x-xsrf-token');
  }
}

?>