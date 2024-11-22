<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'description', 'client_id', 'status'
    ];

    //protected $casts = false;
}
