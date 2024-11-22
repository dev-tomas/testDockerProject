<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Taxe extends Model
{
    protected $fillable = [
        'description', 'value'
    ];

    public $timestamps = false;
}
