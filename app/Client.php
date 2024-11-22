<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'document', 'status', 'plan_id', 'trade_name', 'business_name', 'address', 'detraction', 'invoice_size', 'retention_size',
        'ticket_size', 'perception_size', 'price_type', 'automatic_consumption_surcharge', 'automatic_consumption_surcharge_price',
        'jungle_region_goods', 'jungle_region_services', 'consumption_tax_plastic_bags', 'consumption_tax_plastic_bags_price',
        'observation', 'issue_with_previous_data', 'issue_with_previous_data_days', 'certificate', 'clave_sol', 'usuario_sol',
        'expiration_certificate', 'email', 'phone', 'igv_percentage', 'days_to_send_collections_notifications'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function coins()
    {
    	return $this->belongsToMany('App\Coin', 'bank_accounts');
    }

    public function banks()
    {
    	return $this->belongsToMany('App\BankAccountType', 'bank_accounts', 'client_id', 'bank_account_type_id');
    }

    public function payment_methods()
    {
        return $this->hasMany('App\PaymentMethod');
    }

    public function document_type()
    {
        return $this->belongsTo('App\TypeDocument', 'typedocument_id');
    }

    public function accountsBank()
    {
        return $this->hasMany('App\BankAccount', 'client_id');
    }

    public function sunatCredentials()
    {
        return $this->hasOne('App\ClientCredential');
    }
}
