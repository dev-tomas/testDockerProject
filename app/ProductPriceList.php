<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductPriceList extends Model
{
    public function price_list() {
        return $this->belongsTo('App\PriceList');
    }
}
