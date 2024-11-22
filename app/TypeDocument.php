<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeDocument extends Model
{
    protected $table = 'typedocuments';
    protected $fillable = [
        'description'
    ];
}
