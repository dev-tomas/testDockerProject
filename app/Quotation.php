<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'serial_number', 'date','correlative','exonerated','unaffected','taxed','igv','free',
        'othercharge','discount','total', 'status','issue','expiration','user_id', 'change_type',
        'typevoucher_id','customer_id','headquarter_id', 'sendemail', 'product_region', 'service_region'
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function headquarter() {
        return $this->hasOne('App\HeadQuarter', 'id', 'headquarter_id');
    }

    public function customer() {
        return $this->hasOne('App\Customer', 'id', 'customer_id');
    }

    public function coin()
    {
        return $this->hasOne('App\Coin', 'id', 'coin_id');
    }

    public function payments()
    {
        return $this->hasMany('App\QuotationPayment');
    }

    public function detalles()
    {
        return $this->hasMany('App\QuotationDetail', 'quotation_id', 'id');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
