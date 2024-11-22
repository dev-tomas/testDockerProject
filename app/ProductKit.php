<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductKit extends Model
{
    public function productSale(): BelongsTo
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany('App\ProductKitDetail');
    }

    public function countItems(): int
    {
        return $this->details()->count();
    }
}
