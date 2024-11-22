<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class HeadQuarter extends Model
{
    protected $table = 'headquarters';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 'status', 'client_id', 'ubigeo_id', 'address', 'code', 'main'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.blade.php.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function client()
    {
        return $this->hasOne('App\Client', 'id', 'client_id');
    }

    public function ubigeo()
    {
        return $this->belongsTo('App\Ubigeo');
    }

    public function ubigeos()
    {
        return $this->hasOne('App\Ubigeo', 'id', 'ubigeo_id');
    }

    public function correlatives()
    {
        return $this->hasMany('App\Correlative', 'headquarter_id', 'id');
    }

    public function warehouse()
    {
        return $this->hasOne('App\Warehouse', 'id','warehouse_id');
    }
    public function almacen()
    {
        return $this->hasOne('App\Warehouse');
    }

}
