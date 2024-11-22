<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    protected $fillable = [
        'description', 'symbol', 'code', 'code_str'
    ];

    public function bank_account_types()
    {
    	return $this->belongsToMany('App\BankAccountType', 'bank_accounts');
    }

    public function clients()
    {
    	return $this->belongsToMany('App\Client', 'bank_accounts');
    }
}
