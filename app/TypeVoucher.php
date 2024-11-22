<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeVoucher extends Model
{
    protected $table = 'typevouchers';
    protected $fillable = [
        'description'
    ];
}
