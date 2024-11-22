<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CreditNoteDetail extends Model
{
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function type_igv() {
        return $this->belongsTo('App\IgvType', 'type_igv_id');
    }
}
