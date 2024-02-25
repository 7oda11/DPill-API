<?php

namespace App\Http\Middleware;

use App\Helpers\MyTokenManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MyAuthApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (MyTokenManager::currentUser($request)) {
            return $next($request);
        }else{
            return response(['error'=>'you are unauthorized'],401);
        }
    }
}
