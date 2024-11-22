<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ValidateUserController extends Controller
{
    public function validatePin(Request $request)
    {
        return response()->json(auth()->user()->pin == $request->pin);
    }
}
