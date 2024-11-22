<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $headquarter;
    public function __construct()
    {
        parent::__construct();
        $this->headquarter = null;
        if (auth()->check()) {
            $this->headquarter = session()->has('headlocal') ? session()->get('headlocal') : Auth::user()->headquarter_id;
        }
    }

    protected $fillable = [
        'description', 'code', 'internalcode', 'price', 'status', 'measure_id', 'category_id',
        'client_id', 'brand_id', 'id', 'coin_id', 'sunat_code', 'operation_type', 'exonerated', 'cost', 'utility','tax_id','initial_date','initial_stock', 'is_kit'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function measure()
    {
        return $this->hasOne('App\Measure', 'id', 'measure_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Category', 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo('App\Brand', 'brand_id');
    }

    public function coin()
    {
        return $this->hasOne('App\Coin', 'id', 'coin_id');
    }

    public function stock() {
        $currentWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        return $this->hasOne('App\Store','product_id')->where('warehouse_id', $currentWarehouse->id);
    }

    public function stockByWarehouse($warehouse)
    {
        return $this->hasOne('App\Store','product_id')->where('warehouse_id', $warehouse)->first();
    }

    public function ot()
    {
        return $this->hasOne('App\OperationType', 'id', 'operation_type');
    }

    public function centerCost()
    {
        return $this->belongsTo('App\CostsCenter', 'cost_center_id', 'id');
    }

    public function product_price_list() {
        return $this->hasMany('App\ProductPriceList');
    }

    public function product_price_log() {
        return $this->hasMany('App\ProductPriceLog');
    }

    public function operation_type() {
        return $this->belongsTo('App\OperationType', 'operation_type');
    }

    public function coin_product() {
        return $this->belongsTo('App\Coin', 'coin_id');
    }

    public function type_igv() {
        return $this->belongsTo('App\IgvType', 'type_igv_id');
    }

    public function tax()
    {
        return $this->belongsTo('App\Taxe');
    }

    public function classification() {
        return $this->belongsTo(Classification::class);
    }

    public function stockGlobal() {
        return $this->hasMany('App\Store', 'product_id', 'id');
    }
}
