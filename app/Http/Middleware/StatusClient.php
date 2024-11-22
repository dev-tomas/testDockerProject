<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class StatusClient
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
        if (session()->has('saou')) {
            return $next($request);
        } else if (auth()->check() && auth()->user()->headquarter->client->status == 0) {
            Auth::logout();
	        return redirect()->route('login');
        }
        
        return $next($request);
    }
}
