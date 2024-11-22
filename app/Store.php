<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'stock', 'warehouse_id', 'product_id', 'price', 'higher_price', 'maximum_stock', 'minimum_stock'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function warehouse() {
        return $this->belongsTo('App\Warehouse');
    }
}
