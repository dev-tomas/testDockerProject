<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferenceGuideDetail extends Model
{
    protected $table = 'reference_guides_details';

    public function product()
    {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }
}
