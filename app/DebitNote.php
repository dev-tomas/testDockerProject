<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DebitNote extends Model
{
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function detail()
    {
        return $this->hasMany('App\DebitNoteDetail', 'debit_note_id');
    }

    public function sale()
    {
        return $this->hasOne('App\Sale', 'id', 'sale_id');
    }

    public function typeCreditNote()
    {
        return $this->hasOne('App\TypeDebitNote', 'id', 'type_debit_note_id');
    }

    public function customer() {
        return $this->hasOne('App\Customer', 'id', 'customer_id');
    }

    public function type_voucher()
    {
        return $this->hasOne('App\TypeVoucher', 'id', 'typevoucher_id');
    }

    public function sunat_code()
    {
        return $this->belongsTo('App\SunatCode', 'response_sunat', 'id');
    }
}
