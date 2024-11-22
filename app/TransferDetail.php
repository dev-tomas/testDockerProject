<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferDetail extends Model
{
    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
