<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShoppingController extends Controller
{
    public function coin()
    {
        $this->hasOne('App\Coin');
    }
}
