<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_orders';

    public function detail()
    {
        return $this->hasMany('App\PurchaseOrderDetail', 'order_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Provider', 'provider_id');
    }

    public function requirement()
    {
        return $this->hasOne('App\Requirement', 'id', 'requirement_id');
    }
}
