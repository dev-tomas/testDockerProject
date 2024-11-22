<?php

namespace App\Http\Middleware;

use App\ApiRequest;
use App\ClientToken;
use App\IntegrationRequest;
use Closure;
use Illuminate\Http\Request;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->storeRequest($request);
        if (! $request->hasHeader('Authorization')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $providedToken = $request->header('Authorization');

        if ($this->isValidToken($providedToken)) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    private function isValidToken($providedToken): bool
    {
        if (strlen($providedToken) != 60) {
            return false;
        }

        $clientToken = ClientToken::where('token', $providedToken)->first();

        if ($clientToken == null) {
            return false;
        }

        if ($clientToken->status == 'reject') {
            return false;
        }

        return true;
    }

    public function storeRequest(Request $request)
    {
        try {
            $integration = new ApiRequest;
            $integration->host = $request->ip();
            $integration->headers = json_encode($request->header());
            $integration->payload = json_encode($request->all());
            $integration->token = $request->header('Authorization');
            $integration->save();

        } catch (\Exception $e) {

        }
    }
}
