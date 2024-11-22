<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function shopping()
    {
        return $this->belongsTo('App\Shopping', 'shopping_id');
    }
}
