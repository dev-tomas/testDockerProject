<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    protected $table = 'requirements';
    
    public function detail()
    {
        return $this->hasMany('App\RequirementDetails', 'requirement_id');
    }

    public function center()
    {
        return $this->belongsTo('App\CostsCenter', 'centercost_id');
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Warehouse', 'warehouse_id');
    }

    public function provider()
    {
        return $this->hasOne('App\Provider','id', 'provider_id');
    }
}
