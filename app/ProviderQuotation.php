<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProviderQuotation extends Model
{
    protected $table = 'providers_quotations';

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Provider', 'provider_id');
    }
}
