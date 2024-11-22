<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    public function detail()
    {
        return $this->hasMany('App\CreditNoteDetail', 'credit_note_id');
    }

    public function sale()
    {
        return $this->belongsTo('App\Sale');
    }

    public function typeCreditNote()
    {
        return $this->belongsTo('App\TypeCreditNote');
    }

    public function customer() {
        return $this->belongsTo('App\Customer');
    }

    public function type_voucher()
    {
        return $this->belongsTo('App\TypeVoucher', 'typevoucher_id');
    }

    public function sunat_code()
    {
        return $this->belongsTo('App\SunatCode','response_sunat');
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function bank()
    {
        return $this->belongsTo('App\BankAccount', 'bank_account_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo('App\PaymentMethod', 'payment_method_id');
    }

    public function cash()
    {
        return $this->belongsTo('App\Cash', 'cash_id');
    }
}
