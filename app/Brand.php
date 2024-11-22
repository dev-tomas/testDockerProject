<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'description', 'client_id'
    ];

    public $timestamps = false;

    public function products()
    {
        return $this->hasMany('App\Product');
    }
}
