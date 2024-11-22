<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    protected $table = 'purchase_order_detail';

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
