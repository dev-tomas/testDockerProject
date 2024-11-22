<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RetentionDetail extends Model
{
    public $timestamps = false;

    public function retention()
    {
        return $this->belongsTo('App\Retention');
    }

    public function sale()
    {
        return $this->belongsTo('App\Sale');
    }
}
