<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferenceGuide extends Model
{
    protected $table = 'reference_guides';

    public function docTransport()
    {
        return $this->belongsTo('App\TypeDocument', 'transport_type_document_id');
    }

    public function docDriver()
    {
        return $this->belongsTo('App\TypeDocument', 'driver_type_document_id');
    }

    public function docReceiver()
    {
        return $this->belongsTo('App\TypeDocument', 'receiver_type_document_id');
    }

    public function type_voucher()
    {
        return $this->belongsTo('App\TypeVoucher','typevoucher_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    public function headquarter()
    {
        return $this->belongsTo('App\HeadQuarter');
    }

    public function detail()
    {
        return $this->hasMany('App\ReferenceGuideDetail', 'reference_guide_id');
    }

    public function sunat_code()
    {
        return $this->belongsTo('App\SunatCode', 'response_sunat', 'id');
    }

    public function ubigeo_arrival()
    {
        return $this->belongsTo('App\Ubigeo', 'arrival_address_ubigeo');
    }

    public function ubigeo_start()
    {
        return $this->belongsTo('App\Ubigeo', 'start_address_ubigeo');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function sale()
    {
        return $this->belongsTo('App\Sale');
    }
}
