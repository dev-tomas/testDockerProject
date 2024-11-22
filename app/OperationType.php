<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationType extends Model
{
    protected $table = 'operations_type';
    // protected $fillable = [
    //     'description', 'code'
    // ];

    public $timestamps = false;
}
