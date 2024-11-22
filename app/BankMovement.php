<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BankMovement extends Model
{
    public function shopping ()
    {
        return $this->belongsTo('App\Shopping');
    }

    public function purchase(): HasOne
    {
        return $this->hasOne('App\Shopping');
    }

    public function sale(): HasOne
    {
        return $this->hasOne('App\Sale');
    }
}
