<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccountType extends Model
{
    public $timestamps = false;

    public function coins()
    {
    	return $this->belongsToMany('App\Coin', 'bank_accounts');
    }

    public function clients()
    {
    	return $this->belongsToMany('App\Client', 'bank_accounts');
    }
}
