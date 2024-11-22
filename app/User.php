<?php

namespace App;

use Caffeinated\Shinobi\Concerns\HasRolesAndPermissions;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\HeadQuarter;

class User extends Authenticatable
{
    use Notifiable, HasRolesAndPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'headquarter_id', 'status', 'client_id', 'ia'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.blade.php.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function headquarter()
    {
        return $this->hasOne('App\HeadQuarter', 'id', 'headquarter_id');
    }

    public function icons()
    {
        return $this->belongsToMany('App\IconDashboard', 'user_icon_dashboard', 'user_id', 'icon_dashboard_id');
    }
    public function info()
    {
        return $this->hasOne('App\UserInfo', 'user_id', 'id');
    }
}
