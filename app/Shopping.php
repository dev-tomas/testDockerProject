<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shopping extends Model
{
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function provider()
    {
        return $this->hasOne('App\Provider','id', 'provider_id');
    }

    public function coin()
    {
        return $this->hasOne('App\Coin', 'id', 'coin_id');
    }

    public function typeVoucher()
    {
        return $this->belongsTo('App\TypeVoucher', 'type_vouchers_id');
    }

    public function voucher()
    {
        return $this->hasOne('App\TypeVoucher', 'id', 'type_vouchers_id');
    }

    public function detail()
    {
        return $this->hasMany('App\ShoppingDetail', 'shopping_id');
    }

    public function headquarter()
    {
        return $this->belongsTo('App\HeadQuarter');
    }

    public function accounts()
    {
        return $this->hasMany('App\PurchaseAccount');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function credit()
    {
        return $this->hasOne('App\PurchaseCredit', 'purchase_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo('App\BankAccount', 'bank_account_id ');
    }

    public function bankMovement()
    {
        return $this->hasOne('App\BankMovement', 'shopping_id');
    }
}
