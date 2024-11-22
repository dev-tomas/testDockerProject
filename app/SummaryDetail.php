<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SummaryDetail extends Model
{
    public function sale()
    {
        return $this->belongsTo('App\Sale');
    }

    public function summary()
    {
        return $this->hasMany('App\Summary');
    }
}
