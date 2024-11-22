<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequirementDetails extends Model
{
    protected $table = 'requirement_details';

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }
}
