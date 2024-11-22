<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'description', 'address', 'responsable', 'headquarter_id', 'client_id', 'code'
    ];
    

    public function headquarter()
    {
        return $this->belongsTo('App\Headquarter', 'headquarter_id');
    }
}
