<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perception extends Model
{
    public function user_created()
    {
        return $this->belongsTo('App\User','user_created');
    }

    public function user_updated()
    {
        return $this->belongsTo('App\User','user_updated');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function regime()
    {
        return $this->belongsTo('App\Regime');
    }

    public function sunat_code()
    {
        return $this->belongsTo('App\SunatCode','response_sunat');
    }

    public function detail()
    {
        return $this->hasMany('App\PerceptionDetail');
    }

    public function type_voucher()
    {
        return $this->belongsTo('App\TypeVoucher','typevoucher_id');
    }

    public function headquarter()
    {
        return $this->belongsTo('App\HeadQuarter');
    }
}
