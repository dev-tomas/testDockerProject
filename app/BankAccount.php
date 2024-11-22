<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    public function coins()
    {
    	return $this->belongsTo('App\Coin', 'coin_id');
    }
}
