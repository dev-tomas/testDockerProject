<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'date','correlative','subtotal','serialnumber', 'exonerated','unaffected','taxed','igv', 'free', 'order',
        'othercharge','discount','total','status','issue','expiration','user_id','typevoucher_id','customer_id',
        'headquarter_id', 'quotation_id', 'order', 'condition', 'detraction', 'paidout', 'productregion', 'serviceregion',
        'condition_payment'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function referralguide()
    {
        return $this->hasMany('App\SaleReferralGuide', 'sale_id');
    }

    public function customer() {
        return $this->belongsTo('App\Customer');
    }

    public function coin()
    {
        return $this->belongsTo('App\Coin');
    }

    public function type_voucher()
    {
        return $this->belongsTo('App\TypeVoucher', 'typevoucher_id');
    }

    public function sunat_code()
    {
        return $this->belongsTo('App\SunatCode', 'response_sunat', 'id');
    }

    public function operation()
    {
        return $this->belongsTo('App\TypeOperation', 'typeoperation_id', 'id');
    }

    public function credit_note()
    {
        return $this->belongsTo('App\CreditNote');
    }

    public function debit_note()
    {
        return $this->belongsTo('App\DebitNote');
    }

    public function low_communication()
    {
        return $this->belongsTo('App\LowCommunication');
    }

    public function detail()
    {
        return $this->hasMany('App\SaleDetail', 'sale_id');
    }
    public function detailo()
    {
        return $this->hasMany('App\SaleDetail', 'sale_id')->orderBy('total', 'desc');
    }

    public function headquarter()
    {
        return $this->belongsTo('App\HeadQuarter', 'headquarter_id', 'id');
    }

    public function seller()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function credit()
    {
        return $this->hasMany('App\CreditClient','sale_id', 'id');
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

    public function obank()
    {
        return $this->belongsTo('App\BankAccount','other_bank_account_id');
    }

    public function opaymentMethod()
    {
        return $this->belongsTo('App\PaymentMethod','other_payment_method_id');
    }

    public function ocash()
    {
        return $this->belongsTo('App\Cash','other_cash_id');
    }

    public function credito()
    {
        return $this->hasOne('App\CreditClient','sale_id', 'id');
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function payments()
    {
        return $this->hasMany('App\SalePayment');
    }

    public function returned()
    {
        return $this->belongsTo('App\CreditNote', 'return_id', 'id');
    }

    public function bankMovement()
    {
        return $this->belongsTo('App\BankMovement', 'bank_movement_id');
    }
}
