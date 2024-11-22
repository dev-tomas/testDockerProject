<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductKitDetail extends Model
{
    public function product(): BelongsTo
    {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
