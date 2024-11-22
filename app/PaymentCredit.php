<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentCredit extends Model
{
    public function credit()
    {
        return $this->belongsTo('App\CreditClient','credit_client_id');
    }

    public function bank()
    {
        return $this->belongsTo('App\BankAccount','bank_account_id');
    }

    public function cash()
    {
        return $this->belongsTo('App\Cash');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('App\PaymentMethod');
    }
}
