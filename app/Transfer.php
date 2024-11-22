<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    protected $table = 'transfers';

    public function detail()
    {
        return $this->hasMany('App\TransferDetail', 'transfer_id');
    }

    public function warehouseOrigin(): BelongsTo
    {
        return $this->belongsTo('App\Warehouse', 'warehouse_origin');
    }

    public function warehouseDestination(): BelongsTo
    {
        return $this->belongsTo('App\Warehouse', 'warehouse_destination');
    }
}
