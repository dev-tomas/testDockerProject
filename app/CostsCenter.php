<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostsCenter extends Model
{
    protected $table = 'costs_center';

    public function requirement()
    {
        return $this->hasOne('App\Requirement', 'centercost_id');
    }
}
