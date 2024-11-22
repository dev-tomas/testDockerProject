<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cash extends Model
{
    protected $table = 'cashes';

    public function coin()
    {
        return $this->belongsTo('App\Coin');
    }

    public function movement()
    {
        return $this->hasMany('App\CashMovements', 'cash_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_create', 'id');
    }
}
