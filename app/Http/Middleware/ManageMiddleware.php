<?php

namespace App\Http\Middleware;

use Closure;

class ManageMiddleware
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
        if (! auth()->user()->hasRole('superadmin')) {
            abort(401);
        }
        return $next($request);
    }
}
