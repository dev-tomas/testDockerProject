<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Measure extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'description', 'sunat_code', 'code'
    ];

    //protected $casts = false;
}
