<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = [
        'description', 'document', 'phone', 'address', 'typedocument_id', 'client_id',
        'email', 'secondary_email', 'detraction','code','contact'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function document_type()
    {
        return $this->belongsTo('App\TypeDocument', 'typedocument_id');
    }
} 