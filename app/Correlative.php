<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Correlative extends Model
{
    protected $fillable = [
        'serialnumber', 'correlative', 'headquarter_id', 'typevoucher_id', 'contingency', 'visible', 'client_id'
    ];

    public function type_voucher()
    {
        return $this->hasOne('App\TypeVoucher', 'id', 'typevoucher_id');
    }

    public $timestamps = false;
}
