<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Correlative;

class MethodsHelpController extends Controller
{
    public function index() {}

    public function getCorrelative($headquarter, $typeboucher)
    {
        return Correlative::where([
            ['headquarter_id', $headquarter],
            ['typeboucher_id', $typeboucher]
        ])->get();
    }
}
