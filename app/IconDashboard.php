<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IconDashboard extends Model
{
    protected $table = 'icons_dashboard';

    public function user()
    {
        return $this->belongsToMany('App\User');
    }
}
