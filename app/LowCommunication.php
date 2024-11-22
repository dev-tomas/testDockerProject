<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LowCommunication extends Model
{
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function headquarter()
    {
        return $this->belongsTo('App\Headquarter');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function detail()
    {
        return $this->hasOne('App\LowCommunicationDetail');
    }

    public function detail_low()
    {
        return $this->hasMany('App\LowCommunicationDetail');
    }

    public function sunat()
    {
        return $this->belongsTo('App\SunatCode','sunat_code_id','id');
    }
}
