<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    protected $table = 'saledetails';
    protected $fillable = [
        'price','subtotal','quantity','igv','total','product_id','sale_id'
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

    public function type_igv() {
        return $this->belongsTo('App\IgvType', 'type_igv_id');
    }
}
