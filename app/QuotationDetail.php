<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationDetail extends Model
{
    protected $table = 'quotationdetails';
    protected $fillable = [
        'price','subtotal','unity','igv','total','product_id','quotation_id', 'availability'
    ];

    public $timestamps = false;

    public function product()
    {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }

    public function coin()
    {
        return $this->hasOne('App\Coin', 'id', 'coin_id');
    }

    public function producto()
    {
        return $this->belongsTo('App\Product', 'product_id', 'id');
    }
}
