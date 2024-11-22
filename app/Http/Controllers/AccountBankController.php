<?php

namespace App\Http\Controllers;

use App\BankAccount;
use Illuminate\Http\Request;

class AccountBankController extends Controller
{
    public function update(Request $request)
    {
        $account = BankAccount::find($request->bank_account_id);
        $account->bank_name = $request->bank_account_name;
        $account->number = $request->bank_account_number;
        $account->headline = $request->bank_account_headline;
        $account->cci = $request->bank_account_cci;
        $account->observation = $request->bank_account_observation;
        $account->accounting_account = $request->bank_account_account;
        $account->coin_id = $request->bank_account_coin;
        $account->bank_account_type_id = $request->bank_account_type;
        $account->save();

        return response()->json(true);
    }
}
