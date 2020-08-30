<?php

namespace App\Http\Middleware;

use Closure;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $hasAcces = true; //$request->header('apikey') == env('API_KEY');
        if(!$hasAcces){
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
