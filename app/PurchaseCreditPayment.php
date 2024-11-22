<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseCreditPayment extends Model
{
    protected $table = 'purchase_credit_payments';

    public function credit()
    {
        return $this->belongsTo('App\PurchaseCredit','purchase_credit_id');
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
