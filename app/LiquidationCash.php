<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiquidationCash extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function cash()
    {
        return $this->belongsTo('App\Cash');
    }
}
