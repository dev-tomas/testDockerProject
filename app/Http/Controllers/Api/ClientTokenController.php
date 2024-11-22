<?php

namespace App\Http\Controllers\Api;

use App\ClientToken;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Str;

class ClientTokenController extends Controller
{
    public function generateToken(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
        ]);

        $clientId = $request->input('client_id');
        $token = Str::random(60);

        ClientToken::create([
            'token' => $token,
            'client_id' => $clientId,
        ]);

        return response()->json(true);
    }
}
