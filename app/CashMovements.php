<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashMovements extends Model
{
    protected $table = 'cashes_movements';

    public function cash()
    {
        return $this->belongsTo('App\Cash');
    }
}
