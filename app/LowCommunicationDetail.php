<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LowCommunicationDetail extends Model
{
    public function sale()
    {
        return $this->belongsTo('App\Sale', 'sale_id');
    }

    public function credit_note()
    {
        return $this->belongsTo('App\CreditNote');
    }

    public function debit_note()
    {
        return $this->belongsTo('App\DebitNote');
    }

    public function low_communication()
    {
        return $this->belongsTo('App\LowCommunication');
    }


}
