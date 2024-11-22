<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DebitNoteDetail extends Model
{
    public $timestamps = false;

    public function product()
    {
        return $this->hasOne('App\Product', 'id', 'product_id');
    }

    public function type_igv() {
        return $this->belongsTo('App\IgvType', 'type_igv_id');
    }
}
