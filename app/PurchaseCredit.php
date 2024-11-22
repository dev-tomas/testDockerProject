<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseCredit extends Model
{
    protected $table = 'purchase_credit';

    public function shopping()
    {
        return $this->belongsTo('App\Shopping', 'purchase_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Provider', 'provider_id');
    }

    public function payment()
    {
        return $this->hasMany('App\PurchaseCreditPayment', 'purchase_credit_id');
    }
}
