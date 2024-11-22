<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PerceptionDetail extends Model
{
    public $timestamps = false;

    public function perception()
    {
        return $this->belongsTo('App\Perception');
    }

    public function sale()
    {
        return $this->belongsTo('App\Sale');
    }
}
