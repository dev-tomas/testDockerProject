<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Caffeinated\Shinobi\Models\Role as rs;

class Role extends rs
{
    // protected $table = 'roles';
    protected $fillable = ['name', 'slug', 'description', 'special', 'client_id', 'administrable'];
}
