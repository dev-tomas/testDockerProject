<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function detail()
    {
        return $this->hasMany('App\SummaryDetail');
    }

    public function headquarter()
    {
        return $this->belongsTo('App\Headquarter');
    }

    public function sunat_code()
    {
        return $this->belongsTo('App\SunatCode', 'response_sunat', 'id');
    }
}
