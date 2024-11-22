<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditClient extends Model
{
    public function payments()
    {
        return $this->hasMany('App\PaymentCredit');
    }

    public function sale()
    {
        return $this->belongsTo('App\Sale','sale_id');
    }
}
