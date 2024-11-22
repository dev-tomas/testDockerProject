<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function indexCredit()
    {
        return view('accounting.credtinotes.index');
    }

    public function indexDebit()
    {
        return view('accounting.debitnotes.index');
    }
}
