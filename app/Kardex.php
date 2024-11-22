<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    protected $table = 'kardex';

    public function warehouse()
    {
        return $this->hasOne('App\Warehouse', 'warehouse_id');
    }

    public function product()
    {
        return $this->hasOne('App\Product', 'product_id');
    }
}
