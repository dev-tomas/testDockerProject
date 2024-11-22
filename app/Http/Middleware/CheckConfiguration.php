<?php

namespace App\Http\Middleware;

use App\Client;
use Closure;

class CheckConfiguration
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
        $client = Client::find(auth()->user()->headquarter->client_id);
    }
}
