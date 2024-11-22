<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model
{
    public $timestamps = false;
    public function headquarter()
    {
        return $this->hasMany('App\Headquarter');
    }
}
