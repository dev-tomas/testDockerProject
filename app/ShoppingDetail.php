<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShoppingDetail extends Model
{
    //

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function shopping()
    {
        return $this->belongsTo('App\Shopping', 'shopping_id');
    }

    public function centerCost()
    {
        return $this->belongsTo('App\CostsCenter');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse');
    }
}
