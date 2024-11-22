<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientToken extends Model
{
    protected $fillable = ['token', 'client_id'];

    public function client()
    {
        return $this->belongsTo('App\Client');
    }
}
