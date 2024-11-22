<?php

namespace App\Http\Controllers;

use App\Exports\ReceiptsFeesBookExport;
use App\Sale;
use App\Product;
use App\Shopping;
use App\DebitNote;
use Carbon\Carbon;
use App\CreditNote;
use App\BankAccount;
use App\CostsCenter;
use App\CreditClient;
use App\PaymentCredit;
use App\PaymentMethod;
use App\PurchaseCredit;
use App\ShoppingDetail;
use App\PurchaseAccount;
use Carbon\CarbonPeriod;
use App\AccountingAccount;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\PurchaseCreditPayment;
use Illuminate\Support\Facades\DB;
use App\Exports\FinancesBookExport;
use App\Exports\SaleInterfaceExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseInterfaceExport;
use App\Exports\SalesInterfaceBookExport;
use App\Exports\ProviderExpensesBookExport;
use App\Exports\PurchaseInterfaceBookExport;

class AccountingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexPurchase()
    {
        return view('accountancy.purchase.index');
    }

    public function generatePreview(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d') . ' 00:00:00';
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d') . ' 00:00:00';

        $shoppings = Shopping::with('provider', 'provider.document_type','voucher')
                        ->whereHas('detail', function($q){
                                $q->orderBy('total', 'DESC');
                            })
                        ->where('status', '!=', 9)
                        ->where('type', 1)
                        ->where('client_id', auth()->user()->headquarter->client_id)
                        ->whereBetween('date', [$from, $to])
                        ->get();

        $data = [];
        $cont = 0;

        foreach ($shoppings as $shopping) {
            $label = $shopping->id;
            $data[$cont]['shopping'] = $label;
            $data[$cont]['document'] = $shopping->provider->document;
            $data[$cont]['provider'] = $shopping->provider->description;
            $data[$cont]['provider_code'] = $shopping->provider->code;
            $data[$cont]['type_voucher'] = $shopping->voucher->code;
            $data[$cont]['serie'] = $shopping->shopping_serie;
            $data[$cont]['correlative'] = $shopping->shopping_correlative;
            $data[$cont]['igv'] = $shopping->igv;
            $data[$cont]['total'] = $shopping->total;
            $data[$cont]['date'] = date('d/m/Y', strtotime($shopping->date));
            $data[$cont]['t'] = 'P';
            $data[$cont]['details'] = [];

            $details = ShoppingDetail::leftJoin('products', 'shopping_details.product_id','products.id')
                        ->leftJoin('warehouses', 'shopping_details.warehouse_id','warehouses.id')
                        ->leftJoin('costs_center', 'shopping_details.center_cost_id','costs_center.id')
                        ->where('shopping_id', $shopping->id)
                        ->selectRaw('shopping_details.type_purchase, warehouses.description as warehouse, costs_center.center, products.account_active_fixed, products.account_expense, products.account_stock_purchase')
                        ->groupBy('shopping_details.type_purchase', 'products.account_active_fixed', 'products.account_expense', 'products.account_stock_purchase', 'warehouses.description', 'costs_center.center')
                        ->get();

            $contDetail = 0;

            foreach ($details as $detail) {
                $label = "{$detail->type_purchase}-{$detail->account_active_fixed}";
                if ($detail->type_purchase == '1') {
                    $label = "{$detail->type_purchase}-{$detail->account_stock_purchase}-{$detail->warehouse}";
                } else if ($detail->type_purchase == '2') {
                    $label = "{$detail->type_purchase}-{$detail->account_expense}-{$detail->center}";
                }
                if (! array_key_exists($label, $data[$cont]['details'])) {
                    $data[$cont]['details'][$label]['glosa'] = '';
                    $data[$cont]['details'][$label]['account'] = '';
                    $data[$cont]['details'][$label]['location'] = '';
                    $data[$cont]['details'][$label]['type'] = '';
                }
                $glosa = 'COMPRA DE ACTIVO FIJO';
                $account = $detail->account_active_fixed;
                $location = "<strong>CENTRO DE COSTO:</strong> {$detail->center}";
                if ($detail->type_purchase == '1') {
                    $glosa = 'COMPRA DE MERCADERIA';
                    $account = $detail->account_stock_purchase;
                    $location = "<strong>ALMACEN:</strong> {$detail->warehouse}";
                } else if ($detail->type_purchase == '2') {
                    $glosa = 'COMPRA DE GASTO';
                    $account = $detail->account_expense;
                    $location = "<strong>CENTRO DE COSTO:</strong> {$detail->center}";
                }
                $data[$cont]['details'][$label]['glosa'] = $glosa;
                $data[$cont]['details'][$label]['account'] = $account;
                $data[$cont]['details'][$label]['location'] = $location;
                $data[$cont]['details'][$label]['type'] = $detail->type_purchase;

                $contDetail++;
            }        

            $cont++;
        }

        return response()->json($data);
    }

    public function generateExcel(Request $request)
    {
        $deletes = PurchaseAccount::whereIn('shopping_id', $request->shopping)->get();

        foreach ($deletes as $d ) {
            $delete = PurchaseAccount::find($d->id);
            $delete->delete();
        }

        for ($i=0; $i < count($request->account); $i++) { 
            $account = new PurchaseAccount;
            $account->account = $request->account[$i];
            $account->type = $request->type[$i];
            $account->shopping_id = $request->shopping[$i];
            $account->save();
        }

        $movements = $request->movement;
        $vourcher = $request->voucher;
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d') . ' 00:00:00';
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d') . ' 00:00:00';

        $realShoppings = Shopping::with('provider', 'provider.document_type','voucher','detail')
                        ->whereHas('detail', function($q){
                                $q->orderBy('total', 'DESC');
                            })
                        ->where('status', '!=', 9)
                        ->where('type', 1)
                        ->where('client_id', auth()->user()->headquarter->client_id)
                        ->whereBetween('date', [$from, $to])
                        ->get();

        $types = array();
        foreach ($realShoppings as $shopping) {
            foreach ($shopping->detail as $detail) {
                switch ($detail->type_purchase) {
                    case 0:
                        $types = $this->checkGlosa($types, 0);
                        break;
                    case 1:
                        $types = $this->checkGlosa($types, 1);
                        break;
                    case 2:
                        $types = $this->checkGlosa($types, 2);
                        break;
                    default: break;
                }
            }
        }

        $book = [];

        $data = [];
        $cont = 0;
        $shoppings = [];

        $movement = (int) $request->movement;
        $voucher = (int) $request->voucher;

        foreach ($realShoppings as $shopping) {
            $label = $shopping->id;
            $shoppings[] = $label;

            $book['head'][$cont]['shopping'] = $label;
            $book['head'][$cont]['movement'] = str_pad($movement, 10, '0', STR_PAD_LEFT);
            $book['head'][$cont]['year'] = date('Y', strtotime($shopping->date));
            $book['head'][$cont]['period'] = date('m', strtotime($shopping->date));
            $book['head'][$cont]['book_type'] = '06';
            $book['head'][$cont]['voucher'] = str_pad($voucher, 6, '0', STR_PAD_LEFT);
            $book['head'][$cont]['date'] = date('d/m/Y', strtotime($shopping->date));
            $book['head'][$cont]['glosa'] = $this->getNameGlosa($types);
            $book['head'][$cont]['coin'] = $shopping->coin_id == 1 ? '038' : '040';

            $data[$cont] = [];

            $details = ShoppingDetail::with('product', 'centerCost', 'warehouse', 'shopping')
                                    ->where('shopping_id', $shopping->id)
                                    ->get();

            $contDetail = 0;
            foreach ($details as $detail) {
                $label = "{$detail->shopping_id}-{$detail->type_purchase}-{$detail->product->account_active_fixed}";
                if ($detail->type_purchase == '1') {
                    $label = "{$detail->shopping_id}-{$detail->type_purchase}-{$detail->product->account_stock_purchase}-{$detail->warehouse->code}";
                } else if ($detail->type_purchase == '2') {
                    $label = "{$detail->shopping_id}-{$detail->type_purchase}-{$detail->product->account_expense}-{$detail->centerCost->code}";
                }
                if (! array_key_exists($label, $data[$cont])) {
                    $data[$cont][$label]['glosa'] = '';
                    $data[$cont][$label]['account'] = '';
                    $data[$cont][$label]['location'] = '';
                    $data[$cont][$label]['type'] = '';
                    $data[$cont][$label]['total'] = 0;
                    $data[$cont][$label]['shopping_id'] = $detail->shopping_id;
                }
                $glosa = 'COMPRA DE ACTIVO FIJO';
                $account = $detail->product->account_active_fixed;
                $location = $detail->centerCost != null ? $detail->centerCost->code : null;
                if ($detail->type_purchase == '1') {
                    $glosa = 'COMPRA DE MERCADERIA';
                    $account = $detail->product->account_stock_purchase;
                    $location = "{$detail->warehouse->code}";
                } else if ($detail->type_purchase == '2') {
                    $glosa = 'COMPRA DE GASTO';
                    $account = $detail->product->account_expense;
                    $location = $detail->centerCost != null ? $detail->centerCost->code : null;
                }
                $data[$cont][$label]['glosa'] = $glosa;
                $data[$cont][$label]['account'] = $account;
                $data[$cont][$label]['location'] = $location;
                $data[$cont][$label]['type'] = $detail->type_purchase;
                $data[$cont][$label]['total'] = (float) $detail->subtotal + (float) $data[$cont][$label]['total'];
                $contDetail++;
            }

            $movement = $movement + 1;
            $voucher = $voucher + 1;

            $cont++;
        }

        $movement = (int) $request->movement;
        $voucher = (int) $request->voucher;
        $cont = 0;

        foreach ($data as $key => $d) {
            $sid = 0;
            $totalGen = 0;
            $contItem = 1;
            foreach ($d as $de) {
                $shopping = Shopping::find($de['shopping_id']);
                $sid = $shopping->id;
                $totalGen = (float) $de['total'] + (float) $totalGen;

                $book['body'][$cont]['shopping_id'] = $shopping->id;
                $book['body'][$cont]['movement'] = str_pad($movement, 10, '0', STR_PAD_LEFT);
                $book['body'][$cont]['year'] = date('Y', strtotime($shopping->date));
                $book['body'][$cont]['period'] = date('m', strtotime($shopping->date));
                $book['body'][$cont]['book_type'] = '06';
                $book['body'][$cont]['voucher'] = str_pad($voucher, 6, '0', STR_PAD_LEFT);
                $book['body'][$cont]['account'] = $de['account'];
                $book['body'][$cont]['item'] = $contItem;
                $book['body'][$cont]['glosa'] = $de['glosa'];
                if ($shopping->coin_id == 1) {
                    $book['body'][$cont]['debe_soles'] = $de['total'];
                    $book['body'][$cont]['haber_soles'] = '';
                    $book['body'][$cont]['tipo_cambio'] = '000';
                    $book['body'][$cont]['debe_dolares'] = '000';
                    $book['body'][$cont]['haber_dolares'] = '000';
                } else {
                    $tc = $shopping->exchange_rate;
                    $book['body'][$cont]['debe_soles'] = number_format( ((float) $tc * (float) $de['total']) ,2,'.','');
                    $book['body'][$cont]['haber_soles'] = '';
                    $book['body'][$cont]['tipo_cambio'] = $tc;
                    $book['body'][$cont]['debe_dolares'] = $de['total'];
                    $book['body'][$cont]['haber_dolares'] = '';
                }
                $book['body'][$cont]['cos_codigo'] = $de['location'];
                $book['body'][$cont]['tipo_entidad'] = 'P';
                $book['body'][$cont]['cod_entidad'] = $shopping->provider->code;
                $book['body'][$cont]['doc_type'] = $shopping->voucher->code;
                $book['body'][$cont]['date'] = date('d/m/Y', strtotime($shopping->date));
                $book['body'][$cont]['serie'] = $shopping->shopping_serie;
                $book['body'][$cont]['correlative'] = str_pad($shopping->shopping_correlative, 8, '0', STR_PAD_LEFT);
                $book['body'][$cont]['expiration'] = date('d/m/Y', strtotime($shopping->date));
                $book['body'][$cont]['doc_ref_type'] = '';
                $book['body'][$cont]['doc_ref_date'] = '';
                $book['body'][$cont]['doc_ref_serie'] = '';
                $book['body'][$cont]['doc_ref_correlative'] = '';
                $book['body'][$cont]['mount_unaffected'] = '000';
                $book['body'][$cont]['base_imp'] = '006';
                $book['body'][$cont]['retention'] = '';
                $book['body'][$cont]['date_spot'] = '';
                $book['body'][$cont]['num_spot'] = '';
                $book['body'][$cont]['prov_canc'] = 'P';
                $book['body'][$cont]['opera_tc'] = $shopping->coin_id == 1 ? 'SCV' : 'VEN';;
                $book['body'][$cont]['coin'] = $shopping->coin_id == 1 ? '038' : '040';

                $cont++;
                $contItem++;
            }

            $shopping = Shopping::find($sid);
            $accountIgv = AccountingAccount::where('type', 2)->where('client_id', auth()->user()->headquarter->client_id)->first();
            // IGV
            $totalIgv = $shopping->total - $totalGen;
            $book['body'][$cont]['shopping_id'] = $shopping->id;
            $book['body'][$cont]['movement'] = str_pad($movement, 10, '0', STR_PAD_LEFT);
            $book['body'][$cont]['year'] = date('Y', strtotime($shopping->date));
            $book['body'][$cont]['period'] = date('m', strtotime($shopping->date));
            $book['body'][$cont]['book_type'] = '06';
            $book['body'][$cont]['voucher'] = str_pad($voucher, 6, '0', STR_PAD_LEFT);
            $book['body'][$cont]['account'] = $accountIgv->account;
            $book['body'][$cont]['item'] = $contItem;
            $book['body'][$cont]['glosa'] = $this->getNameGlosa($types);
            if ($shopping->coin_id == 1) {
                $book['body'][$cont]['debe_soles'] = number_format($totalIgv, 2, '.', '');
                $book['body'][$cont]['haber_soles'] = '';
                $book['body'][$cont]['tipo_cambio'] = '000';
                $book['body'][$cont]['debe_dolares'] = '000';
                $book['body'][$cont]['haber_dolares'] = '000';
            } else {
                $tc = $shopping->exchange_rate;
                $book['body'][$cont]['debe_soles'] = floor(($totalIgv * $tc) * 100) / 100;
                $book['body'][$cont]['haber_soles'] = '';
                $book['body'][$cont]['tipo_cambio'] = $tc;
                $book['body'][$cont]['debe_dolares'] = number_format($totalIgv, 2, '.', '');
                $book['body'][$cont]['haber_dolares'] = '';
            }
            $book['body'][$cont]['cos_codigo'] = '';
            $book['body'][$cont]['tipo_entidad'] = '';
            $book['body'][$cont]['cod_entidad'] = '';
            $book['body'][$cont]['doc_type'] = $shopping->voucher->code;
            $book['body'][$cont]['date'] = date('d/m/Y', strtotime($shopping->date));
            $book['body'][$cont]['serie'] = $shopping->shopping_serie;
            $book['body'][$cont]['correlative'] = str_pad($shopping->shopping_correlative, 8, '0', STR_PAD_LEFT);
            $book['body'][$cont]['expiration'] = date('d/m/Y', strtotime($shopping->date));
            $book['body'][$cont]['doc_ref_type'] = '';
            $book['body'][$cont]['doc_ref_date'] = '';
            $book['body'][$cont]['doc_ref_serie'] = '';
            $book['body'][$cont]['doc_ref_correlative'] = '';
            $book['body'][$cont]['mount_unaffected'] = '000';
            $book['body'][$cont]['base_imp'] = '006';
            $book['body'][$cont]['retention'] = '';
            $book['body'][$cont]['date_spot'] = '';
            $book['body'][$cont]['num_spot'] = '';
            $book['body'][$cont]['prov_canc'] = '';
            $book['body'][$cont]['opera_tc'] = $shopping->coin_id == 1 ? 'SCV' : 'VEN';
            $book['body'][$cont]['coin'] = $shopping->coin_id == 1 ? '038' : '040';

            $cont++;
            $contItem++;

            // TOTAL
            $book['body'][$cont]['shopping_id'] = $shopping->id;
            $book['body'][$cont]['movement'] = str_pad($movement, 10, '0', STR_PAD_LEFT);
            $book['body'][$cont]['year'] = date('Y', strtotime($shopping->date));
            $book['body'][$cont]['period'] = date('m', strtotime($shopping->date));
            $book['body'][$cont]['book_type'] = '06';
            $book['body'][$cont]['voucher'] = str_pad($voucher, 6, '0', STR_PAD_LEFT);
            $book['body'][$cont]['account'] = '4212101';
            $book['body'][$cont]['item'] = $contItem;
            $book['body'][$cont]['glosa'] = $this->getNameGlosa($types);
            if ($shopping->coin_id == 1) {
                $book['body'][$cont]['debe_soles'] = '0';
                $book['body'][$cont]['haber_soles'] = number_format($shopping->total, 2, '.', '');
                $book['body'][$cont]['tipo_cambio'] = '000';
                $book['body'][$cont]['debe_dolares'] = '000';
                $book['body'][$cont]['haber_dolares'] = '000';
            } else {
                $tc = $shopping->exchange_rate;
                $book['body'][$cont]['debe_soles'] = '0';
                $book['body'][$cont]['haber_soles'] = floor((($totalGen + $totalIgv) * $tc) * 100) / 100;
                $book['body'][$cont]['tipo_cambio'] = $tc;
                $book['body'][$cont]['debe_dolares'] = '000';
                $book['body'][$cont]['haber_dolares'] = number_format($shopping->total, 2, '.', '');
            }
            $book['body'][$cont]['cos_codigo'] = '';
            $book['body'][$cont]['tipo_entidad'] = 'P';
            $book['body'][$cont]['cod_entidad'] = $shopping->provider->code;
            $book['body'][$cont]['doc_type'] = $shopping->voucher->code;
            $book['body'][$cont]['date'] = date('d/m/Y', strtotime($shopping->date));
            $book['body'][$cont]['serie'] = $shopping->shopping_serie;
            $book['body'][$cont]['correlative'] = str_pad($shopping->shopping_correlative, 8, '0', STR_PAD_LEFT);
            $book['body'][$cont]['expiration'] = date('d/m/Y', strtotime($shopping->date));
            $book['body'][$cont]['doc_ref_type'] = '';
            $book['body'][$cont]['doc_ref_date'] = '';
            $book['body'][$cont]['doc_ref_serie'] = '';
            $book['body'][$cont]['doc_ref_correlative'] = '';
            $book['body'][$cont]['mount_unaffected'] = '000';
            $book['body'][$cont]['base_imp'] = '006';
            $book['body'][$cont]['retention'] = '';
            $book['body'][$cont]['date_spot'] = '';
            $book['body'][$cont]['num_spot'] = '';
            $book['body'][$cont]['prov_canc'] = 'p';
            $book['body'][$cont]['opera_tc'] = $shopping->coin_id == 1 ? 'SCV' : 'VEN';
            $book['body'][$cont]['coin'] = $shopping->coin_id == 1 ? '038' : '040';

            $cont++;
            $contItem++;

            $movement = $movement + 1;
            $voucher = $voucher + 1;
        }

        return Excel::download(new PurchaseInterfaceBookExport($book, 1), 'INTERFAZ DE COMPRAS.xlsx');
    }

    public function indexPurchaseNewVersion()
    {
        return view('accountancy.purchase.v2136.index');
    }

    public function generatePreviewNewVersion(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d') . ' 00:00:00';
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d') . ' 00:00:00';

        $shoppings = Shopping::with('provider', 'provider.document_type','voucher')
                        ->whereHas('detail', function($q){
                                $q->orderBy('total', 'DESC');
                            })
                        ->where('type', 1)
                        ->where('client_id', auth()->user()->headquarter->client_id)
                        ->whereBetween('date', [$from, $to])
                        ->where('status', '!=', 9)
                        ->get();

        $data = [];
        $cont = 0;

        foreach ($shoppings as $shopping) {
            $label = $shopping->id;
            $data[$cont]['shopping'] = $label;
            $data[$cont]['document'] = $shopping->provider->document;
            $data[$cont]['provider'] = $shopping->provider->description;
            $data[$cont]['provider_code'] = $shopping->provider->code;
            $data[$cont]['type_voucher'] = $shopping->voucher->code;
            $data[$cont]['serie'] = $shopping->shopping_serie;
            $data[$cont]['correlative'] = $shopping->shopping_correlative;
            $data[$cont]['igv'] = $shopping->igv;
            $data[$cont]['total'] = $shopping->total;
            $data[$cont]['date'] = date('d/m/Y', strtotime($shopping->date));
            $data[$cont]['t'] = 'P';
            $data[$cont]['details'] = [];

            $details = ShoppingDetail::leftJoin('products', 'shopping_details.product_id','products.id')
                        ->leftJoin('warehouses', 'shopping_details.warehouse_id','warehouses.id')
                        ->leftJoin('costs_center', 'shopping_details.center_cost_id','costs_center.id')
                        ->where('shopping_id', $shopping->id)
                        ->selectRaw('shopping_details.type_purchase, warehouses.description as warehouse, costs_center.center, products.account_active_fixed, products.account_expense, products.account_stock_purchase, products.description')
                        ->groupBy('shopping_details.type_purchase', 'products.account_active_fixed', 'products.account_expense', 'products.account_stock_purchase', 'warehouses.description', 'costs_center.center')
                        ->get();

            $contDetail = 0;

            foreach ($details as $detail) {
                $label = "{$detail->type_purchase}-{$detail->account_active_fixed}";
                if ($detail->type_purchase == '1') {
                    $label = "{$detail->type_purchase}-{$detail->account_stock_purchase}-{$detail->warehouse}";
                } else if ($detail->type_purchase == '2') {
                    $label = "{$detail->type_purchase}-{$detail->account_expense}-{$detail->center}";
                }
                if (! array_key_exists($label, $data[$cont]['details'])) {
                    $data[$cont]['details'][$label]['glosa'] = '';
                    $data[$cont]['details'][$label]['account'] = '';
                    $data[$cont]['details'][$label]['location'] = '';
                    $data[$cont]['details'][$label]['type'] = '';
                }
                $productLabel = Str::upper($detail->description);

                $glosa = "ACTIVO FIJO DE {$productLabel}";
                $account = $detail->account_active_fixed;
                $location = "<strong>CENTRO DE COSTO:</strong> {$detail->center}";
                if ($detail->type_purchase == '1') {
                    $glosa = "COMPRA DE {$productLabel}";
                    $account = $detail->account_stock_purchase;
                    $location = "<strong>ALMACEN:</strong> {$detail->warehouse}";
                } else if ($detail->type_purchase == '2') {
                    $glosa = "GASTO DE {$productLabel}";
                    $account = $detail->account_expense;
                    $location = "<strong>CENTRO DE COSTO:</strong> {$detail->center}";
                }
                $data[$cont]['details'][$label]['glosa'] = $glosa;
                $data[$cont]['details'][$label]['account'] = $account;
                $data[$cont]['details'][$label]['location'] = $location;
                $data[$cont]['details'][$label]['type'] = $detail->type_purchase;

                $contDetail++;
            }        

            $cont++;
        }

        return response()->json($data);
    }

    public function generateExcelNewVersion(Request $request)
    {
        $deletes = PurchaseAccount::whereIn('shopping_id', $request->shopping)->get();

        foreach ($deletes as $d ) {
            $delete = PurchaseAccount::find($d->id);
            $delete->delete();
        }

        for ($i=0; $i < count($request->account); $i++) { 
            $account = new PurchaseAccount;
            $account->account = $request->account[$i];
            $account->type = $request->type[$i];
            $account->shopping_id = $request->shopping[$i];
            $account->save();
        }

        $movements = $request->movement;
        $vourcher = $request->voucher;
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d') . ' 00:00:00';
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d') . ' 00:00:00';

        $realShoppings = Shopping::with('provider', 'provider.document_type','voucher','detail')
                        ->whereHas('detail', function($q){
                                $q->orderBy('total', 'DESC');
                            })
                        ->where('type', 1)
                        ->where('status', '!=', 9)
                        ->where('client_id', auth()->user()->headquarter->client_id)
                        ->whereBetween('date', [$from, $to])
                        ->get();

        $types = array();
        foreach ($realShoppings as $shopping) {
            foreach ($shopping->detail as $detail) {
                switch ($detail->type_purchase) {
                    case 0:
                        $types = $this->checkGlosa($types, 0);
                        break;
                    case 1:
                        $types = $this->checkGlosa($types, 1);
                        break;
                    case 2:
                        $types = $this->checkGlosa($types, 2);
                        break;
                    default: break;
                }
            }
        }

        $book = [];

        $data = [];
        $cont = 0;
        $shoppings = [];

        $movement = (int) $request->movement;

        foreach ($realShoppings as $shopping) {
            $label = $shopping->id;
            $shoppings[] = $label;

            $details = ShoppingDetail::with('product', 'centerCost', 'warehouse', 'shopping')
                                        ->where('shopping_id', $shopping->id)
                                        ->get();

            if (isset($details[0])) {
                $det = $details[0];
                $productLabel = Str::upper($det->product->description);
                $primaryGlosa = "ACTIVO FIJO DE {$productLabel}";
                if ($detail->type_purchase == '1') {
                    $primaryGlosa = "COMPRA DE {$productLabel}";
                } else if ($detail->type_purchase == '2') {
                    $primaryGlosa = "GASTO DE {$productLabel}";
                }
            } else {
                $primaryGlosa = $this->getNameGlosa($types);
            }

            $book['head'][$cont]['shopping'] = $label;
            $book['head'][$cont]['movement'] = str_pad($movement, 10, '0', STR_PAD_LEFT);
            $book['head'][$cont]['period'] = date('m', strtotime($shopping->date));
            $book['head'][$cont]['date'] = date('d/m/Y', strtotime($shopping->date));
            $book['head'][$cont]['glosa'] = $primaryGlosa;
            $book['head'][$cont]['coin'] = $shopping->coin_id == 1 ? '038' : '040';

            $data[$cont] = [];

            $contDetail = 0;
            foreach ($details as $detail) {
                $label = "{$detail->shopping_id}-{$detail->type_purchase}-{$detail->product->account_active_fixed}";
                if ($detail->type_purchase == '1') {
                    $label = "{$detail->shopping_id}-{$detail->type_purchase}-{$detail->product->account_stock_purchase}-{$detail->warehouse->code}";
                } else if ($detail->type_purchase == '2') {
                    $label = "{$detail->shopping_id}-{$detail->type_purchase}-{$detail->product->account_expense}-{$detail->centerCost->code}";
                }
                if (! array_key_exists($label, $data[$cont])) {
                    $data[$cont][$label]['glosa'] = '';
                    $data[$cont][$label]['account'] = '';
                    $data[$cont][$label]['location'] = '';
                    $data[$cont][$label]['type'] = '';
                    $data[$cont][$label]['total'] = 0;
                    $data[$cont][$label]['shopping_id'] = $detail->shopping_id;
                }
                $productLabel = Str::upper($detail->product->description);
                $glosa = "ACTIVO FIJO DE {$productLabel}";
                $account = $detail->product->account_active_fixed;
                $location = $detail->centerCost != null ? $detail->centerCost->code : null;
                if ($detail->type_purchase == '1') {
                    $glosa = "COMPRA DE {$productLabel}";
                    $account = $detail->product->account_stock_purchase;
                    $location = "{$detail->warehouse->code}";
                } else if ($detail->type_purchase == '2') {
                    $glosa = "GASTO DE {$productLabel}";
                    $account = $detail->product->account_expense;
                    $location = $detail->centerCost != null ? $detail->centerCost->code : null;
                }
                $data[$cont][$label]['glosa'] = $glosa;
                $data[$cont][$label]['account'] = $account;
                $data[$cont][$label]['location'] = $location;
                $data[$cont][$label]['type'] = $detail->type_purchase;
                $data[$cont][$label]['total'] = (float) $detail->subtotal + (float) $data[$cont][$label]['total'];
                $contDetail++;
            }

            $movement = $movement + 1;

            $cont++;
        }

        $movement = (int) $request->movement;
        $cont = 0;

        foreach ($data as $key => $d) {
            $sid = 0;
            $totalGen = 0;
            $contItem = 1;
            foreach ($d as $de) {
                $shopping = Shopping::find($de['shopping_id']);
                $sid = $shopping->id;
                $tc = (float) $shopping->exchange_rate;

                $totalGen = (float) $de['total'] + (float) $totalGen;
                
                $book['body'][$cont]['shopping_id'] = $shopping->id;
                $book['body'][$cont]['movement'] = str_pad($movement, 10, '0', STR_PAD_LEFT);
                $book['body'][$cont]['period'] = date('m', strtotime($shopping->date));
                $book['body'][$cont]['account'] = $de['account'];
                $book['body'][$cont]['item'] = $contItem;
                $book['body'][$cont]['glosa'] = $de['glosa'];
                if ($shopping->coin_id == 1) {
                    $book['body'][$cont]['debe_soles'] = $de['total'];
                    $book['body'][$cont]['haber_soles'] = '';
                    $book['body'][$cont]['tipo_cambio'] = '000';
                    $book['body'][$cont]['debe_dolares'] = '000';
                    $book['body'][$cont]['haber_dolares'] = '000';
                } else {                    
                    $book['body'][$cont]['debe_soles'] = number_format($de['total'] * $tc, 2, '.', '');
                    $book['body'][$cont]['haber_soles'] = '';
                    $book['body'][$cont]['tipo_cambio'] = $tc;
                    $book['body'][$cont]['debe_dolares'] = $de['total'];
                    $book['body'][$cont]['haber_dolares'] = '';
                }
                $book['body'][$cont]['cos_codigo'] = $de['location'];
                $book['body'][$cont]['tipo_entidad'] = 'P';
                $book['body'][$cont]['cod_entidad'] = $shopping->provider->code;
                $book['body'][$cont]['doc_type'] = $shopping->voucher->code;
                $book['body'][$cont]['date'] = date('d/m/Y', strtotime($shopping->date));
                $book['body'][$cont]['serie'] = $shopping->shopping_serie;
                $book['body'][$cont]['correlative'] = str_pad($shopping->shopping_correlative, 8, '0', STR_PAD_LEFT);
                $book['body'][$cont]['expiration'] = date('d/m/Y', strtotime($shopping->date));
                $book['body'][$cont]['doc_ref_type'] = '';
                $book['body'][$cont]['doc_ref_date'] = '';
                $book['body'][$cont]['doc_ref_serie'] = '';
                $book['body'][$cont]['doc_ref_correlative'] = '';
                $book['body'][$cont]['mount_unaffected'] = '000';
                $book['body'][$cont]['base_imp'] = $shopping->tax_base != null ? $shopping->tax_base : '006';
                $book['body'][$cont]['retention'] = '';
                $book['body'][$cont]['date_spot'] = '';
                $book['body'][$cont]['num_spot'] = '';
                $book['body'][$cont]['prov_canc'] = '';
                $book['body'][$cont]['opera_tc'] = $shopping->coin_id == 1 ? 'SCV' : 'VEN';;
                $book['body'][$cont]['coin'] = $shopping->coin_id == 1 ? '038' : '040';

                $cont++;
                $contItem++;
            }

            $shopping = Shopping::find($sid);
            // IGV
            $totalIgv = $shopping->total - $totalGen;
            $book['body'][$cont]['shopping_id'] = $shopping->id;
            $book['body'][$cont]['movement'] = str_pad($movement, 10, '0', STR_PAD_LEFT);
            $book['body'][$cont]['period'] = date('m', strtotime($shopping->date));
            $book['body'][$cont]['account'] = '4011101';
            $book['body'][$cont]['item'] = $contItem;
            $book['body'][$cont]['glosa'] = $this->getNameGlosa($types);
            if ($shopping->coin_id == 1) {
                $book['body'][$cont]['debe_soles'] = number_format($totalIgv, 2, '.', '');
                $book['body'][$cont]['haber_soles'] = '';
                $book['body'][$cont]['tipo_cambio'] = '000';
                $book['body'][$cont]['debe_dolares'] = '000';
                $book['body'][$cont]['haber_dolares'] = '000';
            } else {
                $tc = $shopping->exchange_rate;
                $book['body'][$cont]['debe_soles'] = floor(($totalIgv * $tc) * 100) / 100;
                $book['body'][$cont]['haber_soles'] = '';
                $book['body'][$cont]['tipo_cambio'] = $tc;
                $book['body'][$cont]['debe_dolares'] = number_format($totalIgv, 2, '.', '');
                $book['body'][$cont]['haber_dolares'] = '';
            }
            $book['body'][$cont]['cos_codigo'] = '';
            $book['body'][$cont]['tipo_entidad'] = '';
            $book['body'][$cont]['cod_entidad'] = '';
            $book['body'][$cont]['doc_type'] = $shopping->voucher->code;
            $book['body'][$cont]['date'] = date('d/m/Y', strtotime($shopping->date));
            $book['body'][$cont]['serie'] = $shopping->shopping_serie;
            $book['body'][$cont]['correlative'] = str_pad($shopping->shopping_correlative, 8, '0', STR_PAD_LEFT);
            $book['body'][$cont]['expiration'] = date('d/m/Y', strtotime($shopping->date));
            $book['body'][$cont]['doc_ref_type'] = '';
            $book['body'][$cont]['doc_ref_date'] = '';
            $book['body'][$cont]['doc_ref_serie'] = '';
            $book['body'][$cont]['doc_ref_correlative'] = '';
            $book['body'][$cont]['mount_unaffected'] = '000';
            $book['body'][$cont]['base_imp'] =  $shopping->tax_base != null ? $shopping->tax_base : '006';
            $book['body'][$cont]['retention'] = '';
            $book['body'][$cont]['date_spot'] = '';
            $book['body'][$cont]['num_spot'] = '';
            $book['body'][$cont]['prov_canc'] = '';
            $book['body'][$cont]['opera_tc'] = $shopping->coin_id == 1 ? 'SCV' : 'VEN';
            $book['body'][$cont]['coin'] = $shopping->coin_id == 1 ? '038' : '040';

            $cont++;
            $contItem++;

            // TOTAL

            $book['body'][$cont]['shopping_id'] = $shopping->id;
            $book['body'][$cont]['movement'] = str_pad($movement, 10, '0', STR_PAD_LEFT);
            $book['body'][$cont]['period'] = date('m', strtotime($shopping->date));
            $book['body'][$cont]['account'] = '4212101';
            $book['body'][$cont]['item'] = $contItem;
            $book['body'][$cont]['glosa'] = $this->getNameGlosa($types);
            if ($shopping->coin_id == 1) {
                $book['body'][$cont]['debe_soles'] = '0';
                $book['body'][$cont]['haber_soles'] = number_format($shopping->total, 2, '.', '');
                $book['body'][$cont]['tipo_cambio'] = '000';
                $book['body'][$cont]['debe_dolares'] = '000';
                $book['body'][$cont]['haber_dolares'] = '000';
            } else {
                $tc = $shopping->exchange_rate;
                $book['body'][$cont]['debe_soles'] = '0';
                $book['body'][$cont]['haber_soles'] = floor((($totalGen + $totalIgv) * $tc) * 100) / 100;
                $book['body'][$cont]['tipo_cambio'] = $tc;
                $book['body'][$cont]['debe_dolares'] = '';
                $book['body'][$cont]['haber_dolares'] = number_format($shopping->total,2, '.', '');
            }
            $book['body'][$cont]['cos_codigo'] = '';
            $book['body'][$cont]['tipo_entidad'] = 'P';
            $book['body'][$cont]['cod_entidad'] = $shopping->provider->code;
            $book['body'][$cont]['doc_type'] = $shopping->voucher->code;
            $book['body'][$cont]['date'] = date('d/m/Y', strtotime($shopping->date));
            $book['body'][$cont]['serie'] = $shopping->shopping_serie;
            $book['body'][$cont]['correlative'] = str_pad($shopping->shopping_correlative, 8, '0', STR_PAD_LEFT);
            $book['body'][$cont]['expiration'] = date('d/m/Y', strtotime($shopping->date));
            $book['body'][$cont]['doc_ref_type'] = '';
            $book['body'][$cont]['doc_ref_date'] = '';
            $book['body'][$cont]['doc_ref_serie'] = '';
            $book['body'][$cont]['doc_ref_correlative'] = '';
            $book['body'][$cont]['mount_unaffected'] = '000';
            $book['body'][$cont]['base_imp'] = '';
            $book['body'][$cont]['retention'] = '';
            $book['body'][$cont]['date_spot'] = '';
            $book['body'][$cont]['num_spot'] = '';
            $book['body'][$cont]['prov_canc'] = '';
            $book['body'][$cont]['opera_tc'] = $shopping->coin_id == 1 ? 'SCV' : 'VEN';
            $book['body'][$cont]['coin'] = $shopping->coin_id == 1 ? '038' : '040';

            $cont++;
            $contItem++;

            $movement = $movement + 1;
        }

        return Excel::download(new PurchaseInterfaceBookExport($book, 2), 'INTERFAZ DE COMPRAS 2.3.16.xlsx');
    }

    public function generateFinancesInterfaz(Request $request)
    {
        $movements = $request->movement;
        $vourcher = $request->voucher;
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $dates = array();
        $sales = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->with('detail.product.stock.warehouse')
            ->whereBetween('date', [$from, $to])
            ->groupBy('date', 'condition_payment')
            ->where('paidout', '!=', '0')
            ->whereNull('other_condition')
            ->whereNull('low_communication_id')
            ->where('condition_payment', 'EFECTIVO')
            ->selectRaw('sum(total) as total, date, condition_payment')
            ->get();

        $salesDevolucion = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->with('detail.product.stock.warehouse', 'returned.cash', 'returned.bank', 'returned.paymentMethod')
            ->whereBetween('date', [$from, $to])
            ->groupBy('date', 'condition_payment')
            ->where('paidout', '!=', '0')
            ->whereNull('other_condition')
            ->whereNull('low_communication_id')
            ->where('condition_payment', 'DEVOLUCION')
//            ->selectRaw('sum(total) as total, date, condition_payment')
            ->get();

        $salesB = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->with('detail.product.stock.warehouse', 'detailo.product.centerCost')
            ->whereBetween('date', [$from, $to])
            ->groupBy('date', 'condition_payment','bank_account_id')
            ->where('paidout', '!=', '0')
            ->whereNull('other_condition')
            ->whereNull('low_communication_id')
            ->where('condition_payment', 'DEPOSITO EN CUENTA')
            ->selectRaw('sum(total) as total, date, condition_payment, bank_account_id')
            ->get();

        $salesM = Sale::with('detail.product.stock.warehouse')
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->whereBetween('date', [$from, $to])
            ->where(function ($query) {
                $query->where('condition_payment', 'TARJETA DE CREDITO')
                    ->orWhere('condition_payment', 'TARJETA DE DEBITO');
            })
            ->groupBy('date', 'condition_payment','payment_method_id', 'client_id')
            ->where('paidout', '!=', '0')
            ->whereNull('other_condition')
            ->whereNull('low_communication_id')
            ->selectRaw('sum(total) as total, date, condition_payment, payment_method_id, client_id')
            ->get();

        $salesBO = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->with('detail.product.stock.warehouse' , 'detailo.product.centerCost')
            ->whereBetween('date', [$from, $to])
            ->groupBy('date', 'other_condition','other_bank_account_id')
            ->where('paidout', '!=', '0')
            ->whereNull('low_communication_id')
            ->where('other_condition', 'DEPOSITO EN CUENTA')
            ->where('condition_payment', 'EFECTIVO')
            ->selectRaw('sum(total) as total, date, other_condition, other_bank_account_id')
            ->get();

        $salesMO = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->with('detail.product.stock.warehouse')
            ->whereBetween('date', [$from, $to])
            ->groupBy('date', 'other_condition','other_payment_method_id')
            ->where('paidout', '!=', '0')
            ->whereNull('low_communication_id')
            ->where('other_condition', 'TARJETA DE CREDITO')
            ->orWhere('other_condition', 'TARJETA DE DEBITO')
            ->where('condition_payment', 'EFECTIVO')
            ->selectRaw('sum(total) as total, date, other_condition, other_payment_method_id')
            ->get();

        $salesFP = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->with('detail.product.stock.warehouse')
            ->whereBetween('date', [$from, $to])
            ->whereNull('low_communication_id')
            ->where('paidout', '2')
            ->orWhere('paidout', '1')
            ->pluck('id');

        $credits = CreditClient::where('client_id', auth()->user()->headquarter->client_id)
            ->whereBetween('date', [$from, $to])
            ->pluck('id');

        $paymentsE = PaymentCredit::whereIn('credit_client_id', $credits)
            ->where('payment_type', 'EFECTIVO')
            ->whereBetween('date', [$from, $to])
            // ->whereHas('credit.sale', function($q) use ($from, $to) {
            //     $q->whereBetween('sales.date', [$from, $to]);
            // })
            ->groupBy('date', 'cash_id','payment_type')
            ->selectRaw('sum(payment) as total, date, cash_id, payment_type')
            ->get();


        $paymentsB = PaymentCredit::whereIn('credit_client_id', $credits)
            ->whereBetween('date', [$from, $to])
            ->where('payment_type', 'DEPOSITO EN CUENTA')
            // ->whereHas('credit.sale', function($q) use ($from, $to) {
            //     $q->whereBetween('sales.date', [$from, $to]);
            // })
            ->groupBy('date', 'bank_account_id','payment_type')
            ->selectRaw('sum(payment) as total, date, bank_account_id, payment_type')
            ->get();

        $paymentsM = PaymentCredit::whereIn('credit_client_id', $credits)
            ->where('payment_type', 'TARJETA DE CREDITO')
            ->orWhere('payment_type', 'TARJETA DE DEBITO')
            ->whereBetween('date', [$from, $to])
            // ->whereHas('credit.sale', function($q) use ($from, $to) {
            //     $q->whereBetween('sales.date', [$from, $to]);
            // })
            ->groupBy('date', 'payment_method_id','payment_type')
            ->selectRaw('sum(payment) as total, date, payment_method_id, payment_type')
            ->get();
        $ds = array();

        $c = 0;
        foreach ($sales as $s ) {
            $ds[$c]['total'] = $s->total;
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = '1011101';
            $ds[$c]['method'] = $s->condition_payment;
            $ds[$c]['transaction'] = '008';
            $ds[$c]['glosa'] = "COBRO DE DOCUMENTOS  - DIA " . date('d', strtotime($s->date)) . "";
            $ds[$c]['type'] = 1;

            $salesDetail = Sale::with('customer','type_voucher', 'bank', 'cash', 'paymentMethod',
                'detail.product.stock.warehouse', 'detailo.product.centerCost')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->where('date', $s->date)
                ->whereNull('low_communication_id')
                ->where('paidout', '!=', 0)
                ->whereNull('other_condition')
                ->where('condition_payment', 'EFECTIVO')
                ->get();

            $cont = 0;

            foreach ($salesDetail as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->serialnumber;
                $ds[$c]['details'][$cont]['correlative'] = $sd->correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->customer->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->customer->document;
                $ds[$c]['details'][$cont]['total'] = $sd->total;
                $ds[$c]['details'][$cont]['method'] = $sd->condition_payment;
                $ds[$c]['details'][$cont]['othermethod'] = $sd->other_condition;
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->type_voucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->type_voucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '1212101';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->detailo[0]->product->centerCost ? $sd->detailo[0]->product->centerCost->code : '';
                $cont++;
            }

            $c++;
        }

        foreach ($salesDevolucion as $s) {
            $ds[$c]['total'] = $s->total;
            $ds[$c]['date'] = $s->date;
            $paymentAccount = '1212102';
            if ($s->returned->sale->payment_method_id != null) {
                $paymentAccount = $s->returned->sale->paymentMethod ? $s->returned->sale->paymentMethod->account : '1212102';
            }
            if ($s->returned->sale->cash_id != null) {
                $paymentAccount = $s->returned->sale->cash ? $s->returned->sale->cash->account : '1212102';
            }
            if ($s->returned->sale->bank_account_id != null) {
                $paymentAccount = $s->returned->sale->bank ? $s->returned->sale->bank->accounting_account : '1212102';
            }

            $ds[$c]['account'] = $paymentAccount;
            $ds[$c]['method'] = $s->condition_payment;
            $ds[$c]['transaction'] = '008';
            $ds[$c]['glosa'] = "COBRO DE DOCUMENTO";
            $ds[$c]['type'] = 1;

            $salesDetail = Sale::with('customer','type_voucher', 'bank', 'cash', 'paymentMethod',
                'detail.product.stock.warehouse', 'detailo.product.centerCost', 'returned')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->where('date', $s->date)
                ->whereNull('low_communication_id')
                ->whereNull('other_condition')
                ->where('condition_payment', 'DEVOLUCION')
                ->get();

            $cont = 0;

            foreach ($salesDetail as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->serialnumber;
                $ds[$c]['details'][$cont]['correlative'] = $sd->correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->customer->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->customer->document;
                $ds[$c]['details'][$cont]['total'] = $sd->total;
                $ds[$c]['details'][$cont]['method'] = $sd->condition_payment;
                $ds[$c]['details'][$cont]['othermethod'] = $sd->other_condition;
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->type_voucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->type_voucher->description;
                $ds[$c]['details'][$cont]['nc_serie'] = $sd->returned->serial_number;
                $ds[$c]['details'][$cont]['nc_correlative'] = $sd->returned->correlative;
                $ds[$c]['details'][$cont]['paymentAccount'] = '1220001';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->detailo[0]->product->centerCost ? $sd->detailo[0]->product->centerCost->code : '';
                $cont++;
            }

            $c++;
        }

        foreach ($salesB as $s ) {
            $bank = BankAccount::where('client_id', auth()->user()->headquarter->client_id)
                ->find($s->bank_account_id);

            $ds[$c]['total'] = $s->total;
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = $bank->accounting_account;
            $ds[$c]['method'] = $s->condition_payment;
            $ds[$c]['transaction'] = '001';
            $ds[$c]['glosa'] = "COBRO DE DOCUMENTOS  - DIA " . date('d', strtotime($s->date)) . "";
            $ds[$c]['type'] = 1;

            $salesDetail = Sale::with('customer','type_voucher', 'bank', 'cash', 'paymentMethod',
                'detail.product.stock.warehouse', 'detailo.product.centerCost', 'bankMovement')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->where('date', $s->date)
                ->whereNull('low_communication_id')
                ->where('paidout', '!=', 0)
                ->whereNull('other_condition')
                ->where('bank_account_id', $bank->id)
                ->where('condition_payment', $s->condition_payment)
                ->get();

            $cont = 0;

            foreach ($salesDetail as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->serialnumber;
                $ds[$c]['details'][$cont]['correlative'] = $sd->correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->customer->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->customer->document;
                $ds[$c]['details'][$cont]['total'] = $sd->total;
                $ds[$c]['details'][$cont]['method'] = $sd->condition_payment;
                $ds[$c]['details'][$cont]['othermethod'] = $sd->other_condition;
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->type_voucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->type_voucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '1212101';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->detailo[0]->product->centerCost == null ? '-' : $sd->detailo[0]->product->centerCost->code;
                $cont++;
            }

            $c++;
        }
        foreach ($salesM as $s ) {
            if ($s != null) {
                $payment = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)
                    ->find($s->payment_method_id);
                if ($payment) {
                    $ds[$c]['total'] = $s->total;
                    $ds[$c]['date'] = $s->date;
                    $ds[$c]['account'] = $payment == null ? '-' : $payment->account;
                    $ds[$c]['method'] = $s->condition_payment;
                    if ($s->condition_payment == 'TARJETA DE CREDITO') {
                        $t = '006';
                    } else {
                        $t = '005';
                    }
                    $ds[$c]['transaction'] = $t;
                    $ds[$c]['glosa'] = "COBRO DE DOCUMENTOS  - DIA " . date('d', strtotime($s->date)) . "";
                    $ds[$c]['type'] = 1;

                    $salesDetail = Sale::with('customer','type_voucher', 'bank', 'cash', 'paymentMethod', 'detail.product.stock.warehouse', 'detailo.product.centerCost')->where('client_id', auth()->user()->headquarter->client_id)
                        ->where('date', $s->date)
                        ->whereNull('low_communication_id')
                        ->where('paidout', '!=', 0)
                        ->whereNull('other_condition')
                        ->where('payment_method_id', $payment->id)
                        ->where('condition_payment', $s->condition_payment)
                        ->get();

                    if ($salesDetail->count() == 0) {
                        $ds[$c]['details'] = [];
                    }

                    $cont = 0;

                    foreach ($salesDetail as $sd) {
                        $ds[$c]['details'][$cont]['serie'] = $sd->serialnumber;
                        $ds[$c]['details'][$cont]['correlative'] = $sd->correlative;
                        $ds[$c]['details'][$cont]['customer'] = $sd->customer->code;
                        $ds[$c]['details'][$cont]['customer_document'] = $sd->customer->document;
                        $ds[$c]['details'][$cont]['total'] = $sd->total;
                        $ds[$c]['details'][$cont]['method'] = $sd->condition_payment;
                        $ds[$c]['details'][$cont]['othermethod'] = $sd->other_condition;
                        $ds[$c]['details'][$cont]['typeDocument'] = $sd->type_voucher->code;
                        $ds[$c]['details'][$cont]['comprobante'] = $sd->type_voucher->description;
                        $ds[$c]['details'][$cont]['paymentAccount'] = '1212101';
                        $ds[$c]['details'][$cont]['expiration'] = "";
                        $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
                        $ds[$c]['details'][$cont]['center'] = $sd->detailo[0]->product->centerCost->code;
                        $cont++;
                    }

                    $c++;
                }
            }
        }

        foreach ($salesBO as $s ) {
            $bank = BankAccount::where('client_id', auth()->user()->headquarter->client_id)
                ->find($s->other_bank_account_id);

            $ds[$c]['total'] = $s->total;
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = $bank->accounting_account;
            $ds[$c]['method'] = $s->other_condition;
            $ds[$c]['transaction'] = '001';
            $ds[$c]['glosa'] = "COBRO DE DOCUMENTOS  - DIA " . date('d', strtotime($s->date)) . "";
            $ds[$c]['type'] = 1;

            $salesDetail = Sale::with('customer','type_voucher', 'bank', 'cash', 'paymentMethod', 'detail.product.stock.warehouse', 'detailo.product.centerCost')->where('client_id', auth()->user()->headquarter->client_id)
                ->where('date', $s->date)
                ->whereNull('low_communication_id')
                ->where('paidout', '!=', 0)
                ->where('condition_payment', 'EFECTIVO')
                ->where('other_condition', $s->other_condition)
                ->get();

            $cont = 0;

            foreach ($salesDetail as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->serialnumber;
                $ds[$c]['details'][$cont]['correlative'] = $sd->correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->customer->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->customer->document;
                $ds[$c]['details'][$cont]['total'] = $sd->total;
                $ds[$c]['details'][$cont]['method'] = $sd->other_condition;
                $ds[$c]['details'][$cont]['othermethod'] = $sd->other_condition;
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->type_voucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->type_voucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '1212101';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->detailo[0]->product->centerCost == null ? "-" : $sd->detailo[0]->product->centerCost->code;
                $cont++;
            }

            $c++;
        }

        foreach ($salesMO as $s ) {
            $payment = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)
                                    ->find($s->other_payment_method_id);
            if ($payment != null) {
                $ds[$c]['total'] = $s->total;
                $ds[$c]['date'] = $s->date;
                $ds[$c]['account'] = $payment->account;
                $ds[$c]['method'] = $s->other_condition;
                if ($s->other_condition == 'TARJETA DE CREDITO') {
                    $t = '006';
                } else {
                    $t = '005';
                }
                $ds[$c]['transaction'] = $t;
                $ds[$c]['glosa'] = "PAGO DE DOCUMENTOS - DIA " . date('d', strtotime($s->date)) . "";
                $ds[$c]['type'] = 1;

                $salesDetail = Sale::with('customer','type_voucher', 'bank', 'cash', 'paymentMethod', 'detail.product.stock.warehouse', 'detailo.product.centerCost')->where('client_id', auth()->user()->headquarter->client_id)
                    ->where('date', $s->date)
                    ->whereNull('low_communication_id')
                    ->where('paidout', '!=', 0)
                    ->orWhere('status_condition', 1)
                    ->where('condition_payment', 'EFECTIVO')
                    ->where('other_condition', $s->other_condition)
                    ->where('other_payment_method_id', $s->other_payment_method_id)
                    ->get();

                $cont = 0;

                foreach ($salesDetail as $sd) {
                    $ds[$c]['details'][$cont]['serie'] = $sd->serialnumber;
                    $ds[$c]['details'][$cont]['correlative'] = $sd->correlative;
                    $ds[$c]['details'][$cont]['customer'] = $sd->customer->code;
                    $ds[$c]['details'][$cont]['customer_document'] = $sd->customer->document;
                    $ds[$c]['details'][$cont]['total'] = $sd->total;
                    $ds[$c]['details'][$cont]['method'] = $sd->other_condition;
                    $ds[$c]['details'][$cont]['othermethod'] = $sd->other_condition;
                    $ds[$c]['details'][$cont]['typeDocument'] = $sd->type_voucher->code;
                    $ds[$c]['details'][$cont]['comprobante'] = $sd->type_voucher->description;
                    $ds[$c]['details'][$cont]['paymentAccount'] = '1212101';
                    $ds[$c]['details'][$cont]['expiration'] = "";
                    $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
                    $ds[$c]['details'][$cont]['center'] = $sd->detailo[0]->product->centerCost ? $sd->detailo[0]->product->centerCost->code : '';;
                    $cont++;
                }

                $c++;
            }
        }

        foreach ($paymentsE as $s ) {
            $ds[$c]['total'] = $s->total;
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = '1011101';
            $ds[$c]['method'] = $s->payment_type;
            $ds[$c]['transaction'] = '008';
            $ds[$c]['glosa'] = "COBRO DE DOCUMENTOS  - DIA " . date('d', strtotime($s->date)) . "";
            $ds[$c]['type'] = 2;

            $salesDetail = PaymentCredit::with('credit','credit.sale.detail.product.stock.warehouse','credit.sale.detailo.product.centerCost', 'credit.sale.customer','bank', 'cash', 'paymentMethod')->where('client_id', auth()->user()->headquarter->client_id)
                ->where('date', $s->date)
                ->where('payment_type', 'EFECTIVO')
                ->get();

            $cont = 0;

            foreach ($salesDetail as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->credit->sale->serialnumber;
                $ds[$c]['details'][$cont]['correlative'] = $sd->credit->sale->correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->credit->sale->customer->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->credit->sale->customer->document;
                $ds[$c]['details'][$cont]['total'] = $sd->payment;
                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->credit->sale->type_voucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->credit->sale->type_voucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '1212102';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->credit->sale->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->credit->sale->detailo[0]->product->centerCost->code;
                $cont++;
            }

            $c++;
        }

        foreach ($paymentsB as $s ) {
            $bank = BankAccount::where('client_id', auth()->user()->headquarter->client_id)
                ->find($s->bank_account_id);

            $ds[$c]['total'] = $s->total;
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = $bank->accounting_account;
            $ds[$c]['method'] = $s->payment_type;
            $ds[$c]['transaction'] = '001';
            $ds[$c]['glosa'] = "PAGO DE DOCUMENTOS  - DIA " . date('d', strtotime($s->date)) . "";
            $ds[$c]['type'] = 2;

            $salesDetail = PaymentCredit::with('credit','credit.sale.detail.product.stock.warehouse','credit.sale.detailo.product.centerCost', 'bank', 'cash', 'paymentMethod')->where('client_id', auth()->user()->headquarter->client_id)
                ->where('date', $s->date)
                ->where('bank_account_id', $bank->id)
                ->where('payment_type', 'DEPOSITO EN CUENTA')
                ->get();

            $cont = 0;

            foreach ($salesDetail as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->credit->sale->serialnumber;
                $ds[$c]['details'][$cont]['correlative'] = $sd->credit->sale->correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->credit->sale->customer->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->credit->sale->customer->document;
                $ds[$c]['details'][$cont]['total'] = $sd->payment;
                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->credit->sale->type_voucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->credit->sale->type_voucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '1212102';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->credit->sale->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->credit->sale->detailo[0]->product->centerCost == null ? "-" : $sd->credit->sale->detailo[0]->product->centerCost->code;
                $cont++;
            }

            $c++;
        }

        foreach ($paymentsM as $s ) {
            $payment = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)
                ->find($s->payment_method_id);

            $ds[$c]['total'] = $s->total;
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = $payment->account;
            $ds[$c]['method'] = $s->payment_type;
            if ($s->payment_type == 'TARJETA DE CREDITO') {
                $t = '006';
            } else {
                $t = '005';
            }
            $ds[$c]['transaction'] = $t;
            $ds[$c]['glosa'] = "PAGO DE DOCUMENTOS  - DIA " . date('d', strtotime($s->date)) . "";
            $ds[$c]['type'] = 2;

            $salesDetail = PaymentCredit::with('credit','credit.sale.detail.product.stock.warehouse', 'credit.sale.detailo.product.centerCost','bank', 'cash', 'paymentMethod')->where('client_id', auth()->user()->headquarter->client_id)
                ->where('date', $s->date)
                ->where('payment_type', 'TARJETA DE CREDITO')
                ->orWhere('payment_type', 'TARJETA DE DEBITO')
                ->get();


            $cont = 0;

            foreach ($salesDetail as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->credit->sale->serialnumber;
                $ds[$c]['details'][$cont]['correlative'] = $sd->credit->sale->correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->credit->sale->customer->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->credit->sale->customer->document;
                $ds[$c]['details'][$cont]['total'] = $sd->payment;
                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->credit->sale->type_voucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->credit->sale->type_voucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '1212102';
                $ds[$c]['details'][$cont]['expiration'] = "-";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->credit->sale->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->credit->sale->detailo[0]->product->centerCost->code;
                $cont++;
            }

            $c++;
        }

        $notes = array();

        $creditNotes = CreditNote::with('sale.type_voucher', 'customer', 'type_voucher',
                                        'sale.paymentMethod', 'sale.cash', 'sale.bank', 'bank', 'paymentMethod', 'cash')
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->whereBetween('date_issue', [$from, $to])
                                ->get();

        $debitNotes = DebitNote::with('sale.type_voucher', 'customer', 'type_voucher')->where('client_id', auth()->user()->headquarter->client_id)
            ->whereBetween('date_issue', [$from, $to])
            ->get();

        foreach ($creditNotes as $creditNote) {
            $register = array();
            if ($creditNote->condition_payment != 'DEVOLUCION') {
                $register['id'] = 1;
                $register['serial_number'] = $creditNote->serial_number;
                $register['correlative'] = $creditNote->correlative;
                $register['customer_code'] = $creditNote->customer->code;
                $register['document_type_code'] = $creditNote->type_voucher->code;
                $register['document_type_code_rel'] = $creditNote->sale->type_voucher->code;
                $register['document_serial_number_rel'] = $creditNote->sale->serialnumber;
                $register['document_correlative_rel'] = $creditNote->sale->correlative;
                $register['total'] = $creditNote->total;
                $register['debe'] = 0;
                $register['haber'] = $creditNote->total;
                $register['item'] = 1;
                $register['date'] = $creditNote->date_issue;
                $register['account'] = '1011101';
                $register['transaction'] = '';
                $register['prov_can'] = '';
                $register['type_document'] = "NOTA DE CRÉDITO " . Str::upper($creditNote->typeCreditNote->description);
                $register['type_document_id'] = 1;
                $register['type'] = 1;
                $register['cos_codigo'] = '';
                array_push($notes, $register);

                $register = array();
                $register['id'] = 2;
                $register['serial_number'] = $creditNote->serial_number;
                $register['correlative'] = $creditNote->correlative;
                $register['customer_code'] = $creditNote->customer->code;
                $register['document_type_code'] = $creditNote->type_voucher->code;
                $register['document_type_code_rel'] = $creditNote->sale->type_voucher->code;
                $register['document_serial_number_rel'] = $creditNote->sale->serialnumber;
                $register['document_correlative_rel'] = $creditNote->sale->correlative;
                $register['debe'] = $creditNote->total;
                $register['haber'] = 0;
                $register['item'] = 2;
                $register['total'] = $creditNote->total;
                $register['date'] = $creditNote->date_issue;
                $register['account'] = '1212101';
                $register['transaction'] = '008';
                $register['prov_can'] = 'C';
                $register['type_document'] = "NOTA DE CRÉDITO " . Str::upper($creditNote->typeCreditNote->description);
                $register['type_document_id'] = 1;
                $register['type'] = 2;
                $register['cos_codigo'] = '001';
                array_push($notes, $register);
            } else {
                $register['id'] = 1;
                $register['serial_number'] = $creditNote->serial_number;
                $register['correlative'] = $creditNote->correlative;
                $register['customer_code'] = $creditNote->customer->code;
                $register['document_type_code'] = $creditNote->type_voucher->code;
                $register['document_type_code_rel'] = $creditNote->sale->type_voucher->code;
                $register['document_serial_number_rel'] = $creditNote->sale->serialnumber;
                $register['document_correlative_rel'] = $creditNote->sale->correlative;
                $register['total'] = 0;
                $register['debe'] = 0;
                $register['haber'] = $creditNote->total;;
                $register['item'] = 1;
                $register['date'] = $creditNote->date_issue;
                $paymentAccount = '1041102';

                if ($creditNote->sale->payment_method_id != null) {
                    $paymentAccount = $creditNote->sale->paymentMethod ? $creditNote->sale->paymentMethod->account : '1041102';
                }
                if ($creditNote->sale->cash_id != null) {
                    $paymentAccount = $creditNote->sale->cash ? $creditNote->sale->cash->account : '1041102';
                }
                if ($creditNote->sale->bank_account_id != null) {
                    $paymentAccount = $creditNote->sale->bank ? $creditNote->sale->bank->accounting_account : '1041102';
                }

                $register['account'] = $paymentAccount;
                $register['transaction'] = '';
                $register['prov_can'] = '';
                $register['type_document'] = "NOTA DE CRÉDITO " . Str::upper($creditNote->typeCreditNote->description);
                $register['type_document_id'] = 1;
                $register['type'] = 1;
                $register['cos_codigo'] = '';
                array_push($notes, $register);

                $register = array();
                $register['id'] = 2;
                $register['serial_number'] = $creditNote->serial_number;
                $register['correlative'] = $creditNote->correlative;
                $register['customer_code'] = $creditNote->customer->code;
                $register['document_type_code'] = $creditNote->type_voucher->code;
                $register['document_type_code_rel'] = $creditNote->sale->type_voucher->code;
                $register['document_serial_number_rel'] = $creditNote->sale->serialnumber;
                $register['document_correlative_rel'] = $creditNote->sale->correlative;
                $register['debe'] = $creditNote->total;
                $register['haber'] = 0;
                $register['item'] = 2;
                $register['total'] = $creditNote->total;
                $register['date'] = $creditNote->date_issue;
                $register['account'] = '1220001';
                $register['transaction'] = '008';
                $register['prov_can'] = 'C';
                $register['type_document'] = "NOTA DE CRÉDITO " . Str::upper($creditNote->typeCreditNote->description);
                $register['type_document_id'] = 1;
                $register['type'] = 2;
                $register['cos_codigo'] = '001';
                array_push($notes, $register);
            }
        }

        foreach ($debitNotes as $debitNote) {
            $register = array();
            $register['id']  =   1;
            $register['serial_number']  =   $debitNote->serial_number;
            $register['correlative']  =   $debitNote->correlative;
            $register['customer_code'] = $debitNote->customer->code;
            $register['document_type_code'] = $debitNote->type_voucher->code;
            $register['document_type_code_rel'] = $debitNote->sale->type_voucher->code;
            $register['document_serial_number_rel'] = $debitNote->sale->serialnumber;
            $register['document_correlative_rel'] = $debitNote->sale->correlative;
            $register['total'] = $debitNote->total;
            $register['debe']   =   $debitNote->total;
            $register['haber']  =   0;
            $register['item']   =   1;
            $register['date'] = $debitNote->date_issue;
            $register['account'] = '1011101';
            $register['transaction'] = '';
            $register['prov_can'] = '';
            $register['type_document'] = 'NOTA DE DÉBITO';
            $register['type_document_id'] = 2;
            $register['type'] = 1;
            $register['cos_codigo'] = '';
            array_push($notes, $register);

            $register = array();
            $register['id']  =   2;
            $register['serial_number']  =   $debitNote->serial_number;
            $register['correlative']  =   $debitNote->correlative;
            $register['customer_code'] = $debitNote->customer->code;
            $register['document_type_code'] = $debitNote->type_voucher->code;
            $register['document_type_code_rel'] = $debitNote->sale->type_voucher->code;
            $register['document_serial_number_rel'] = $debitNote->sale->serialnumber;
            $register['document_correlative_rel'] = $debitNote->sale->correlative;
            $register['total'] = $debitNote->total;
            $register['debe']   =   0;
            $register['haber']  =   $debitNote->total;
            $register['item']   =   2;
            $register['date'] = $debitNote->date_issue;
            $register['account'] = '1212101';
            $register['transaction'] = '008';
            $register['prov_can'] = 'C';
            $register['type_document'] = 'NOTA DE DÉBITO';
            $register['type_document_id'] = 2;
            $register['type'] = 2;
            $register['cos_codigo'] = '001';
            array_push($notes, $register);
        }

        return Excel::download(new FinancesBookExport($sales, $ds, $movements, $vourcher, $notes), 'INTERFAZ CUENTAS POR COBRAR.xlsx');
    }

    public function checkGlosa($types, $glosa) {
        array_push($types, $glosa);
        return array_unique($types);
    }

    public function getBaseImponible($tax_base) {
        $base_imponible = '';
        switch ($tax_base) {
            case '006':
                $base_imponible = 'OPER. GRAVADAS';
                break;
            case '007':
                $base_imponible = 'OPER. GRAV. Y NOGRAVA.';
                break;
            case '008':
                $base_imponible = 'OPER. NOGRAVADAS';
                break;
            case '999':
                $base_imponible = 'EXONERADAS';
                break;
        }

        return $base_imponible;
    }

    public function getNameGlosa($types) {
        $response = '';

        if(count($types) == 1) {
            if($types[0] == 0) {$response .= 'ACTIVO FIJO';}
            if($types[0] == 1) {$response .= 'COMPRA DE MERCADERIA';}
            if($types[0] == 2) {$response .= 'COMPRA DE GASTO'; }
            return $response;
        } else {
            if(array_search(0, $types) !== false) {$response .= ',COMPRA DE ACTIVO FIJO,';}
            if(array_search(1, $types) !== false) {$response .= ',COMPRA DE MERCADERIA,';}
            if(array_search(2, $types) !== false) {$response .= ',COMPRA DE GASTO,'; }
        }
        $response = str_replace(',,', ', ', $response);
        $response = substr($response, 1);
        $lengthResponse = strlen($response);
        $response = substr($response,0, ($lengthResponse - 1));
        return $response;
    }

    public function indexSale()
    {
        return view('accountancy.sale.index');
    }

    public function generateSaleInterfaz(Request $request)
    {
        $movements = $request->movement;
        $vourcher = $request->voucher;
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');
        $sales = array();

        $finalSales = Sale::with('customer','type_voucher','detailo', 'detailo.product', 'detailo.product.centerCost','detailo.product.stock.warehouse', 'credit_note.detail.product.stock.warehouse', 'debit_note.detail.product.stock.warehouse')
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->whereHas('detail', function($query) {
                                    $query->orderBy('total','DESC');
                                })
                                ->whereBetween('date', [$from, $to])
                                ->get();

        $creditnotes = CreditNote::with('sale', 'customer', 'type_voucher', 'detail', 'detail.product', 'detail.product.centerCost', 'detail.product.stock.warehouse')
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->whereHas('detail', function($query) {
                                    $query->orderBy('total','DESC');
                                })
                                ->whereBetween('date_issue', [$from, $to])
                                ->get();

        $debitNotes = DebitNote::with('sale', 'customer', 'type_voucher', 'detail', 'detail.product', 'detail.product.stock.warehouse')
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->whereBetween('date_issue', [$from, $to])
            ->get();

        $ai = AccountingAccount::where('type', 2)->where('client_id', auth()->user()->headquarter->client_id)->first();
        $ar = AccountingAccount::where('type', 3)->where('client_id', auth()->user()->headquarter->client_id)->first();
        $ab = AccountingAccount::where('type', 4)->where('client_id', auth()->user()->headquarter->client_id)->first();

        if ($ai == null) {
            toastr()->warning('No tiene configurada la cuenta contable para IGV.');
            return redirect()->back();
        }

        if ($ar == null) {
            toastr()->warning('No tiene configurada la cuenta contable para Recargo al consumo.');

            return redirect()->back();
        }

        return Excel::download(new SalesInterfaceBookExport($finalSales, $movements, $vourcher, $ai, $ar,$ab, $creditnotes, $debitNotes, 1), 'INTERFAZ DE VENTAS.xlsx');
    }

    public function indexSaleNewVersion()
    {
        return view('accountancy.sale.v2136.index');
    }

    public function generateSaleInterfazNewVersion(Request $request)
    {
        $movements = $request->movement;
        $vourcher = 0;
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');
        $sales = array();

        $finalSales = Sale::with('customer','type_voucher','detailo', 'detailo.product', 'detailo.product.centerCost','detailo.product.stock.warehouse', 'credit_note.detail.product.stock.warehouse', 'debit_note.detail.product.stock.warehouse')
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->whereHas('detail', function($query) {
                                    $query->orderBy('total','DESC');
                                })
                                ->whereBetween('date', [$from, $to])
                                ->get();

        $creditnotes = CreditNote::with('sale', 'customer', 'type_voucher', 'detail', 'detail.product', 'detail.product.centerCost', 'detail.product.stock.warehouse')
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->whereHas('detail', function($query) {
                                    $query->orderBy('total','DESC');
                                })
                                ->whereBetween('date_issue', [$from, $to])
                                ->get();

        $debitNotes = DebitNote::with('sale', 'customer', 'type_voucher', 'detail', 'detail.product', 'detail.product.stock.warehouse')
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->whereBetween('date_issue', [$from, $to])
            ->get();

        $ai = AccountingAccount::where('type', 2)->where('client_id', auth()->user()->headquarter->client_id)->first();
        $ar = AccountingAccount::where('type', 3)->where('client_id', auth()->user()->headquarter->client_id)->first();
        $ab = AccountingAccount::where('type', 4)->where('client_id', auth()->user()->headquarter->client_id)->first();

        if ($ai == null) {
            toastr()->warning('No tiene configurada la cuenta contable para IGV.');
            return redirect()->back();
        }

        if ($ar == null) {
            toastr()->warning('No tiene configurada la cuenta contable para Recargo al consumo.');

            return redirect()->back();
        }

        return Excel::download(new SalesInterfaceBookExport($finalSales, $movements, $vourcher, $ai, $ar,$ab, $creditnotes, $debitNotes, 2), 'INTERFAZ DE VENTAS 2.13.6.xlsx');
    }

    public function products()
    {
        $centerCosts = CostsCenter::where('client_id', auth()->user()->headquarter->client_id)->get();

        return view('accountancy.products.index', compact('centerCosts'));
    }

    public function dt_products(Request $request)
    {
        $search = $request->get('search2');
        $type = $request->get('type');

        $products = Product::where('client_id', auth()->user()->headquarter->client_id)
                        ->where(function ($query) use ($search, $type) {
                            if($search != null && $search != '') {
                                $query->where('products.description', 'like', '%' . $search. '%');
                            }
                            if ($type != '') {
                                $query->where('type_product', $type);
                            }
                        })->get(['id', 'description','code','internalcode', 'account','cost_center_id', 'account_active_fixed', 'account_expense', 'account_stock_purchase']);


        return datatables()->of($products)->toJson();
    }

    public function store(Request $request)
    {
        $product = Product::find($request->product_id);
        $product->account = $request->account;
        if ($request->account_expense != '') {
            $product->account_expense = $request->account_expense;
        }
        if ($request->account_active_fixed != '') {
            $product->account_active_fixed = $request->account_active_fixed;
        }
        if ($request->account_stock_purchase != '') {
            $product->account_stock_purchase = $request->account_stock_purchase;
        }
        $product->cost_center_id = $request->centercost;
        $product->save();

        return response()->json(true);
    }

    public function indexAccountsReceivable()
    {
        return view('accountancy.finances.index');
    }
    
    public function indexAccountsPendingPurchase()
    {
        return view('accountancy.purchasePayment.index');
    }

    public function generateFinancesPurchaseInterfaz(Request $request)
    {
        $movements = $request->movement;
        $vourcher = $request->voucher;
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $dates = array();

        $purchaseEfectivo = Shopping::where('client_id', auth()->user()->headquarter->client_id)
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->where('status', 1)
            ->where('coin_id', 1)
            ->groupBy('date', 'payment_type')
            ->where('payment_type', 'EFECTIVO')
            ->selectRaw('sum(total) as total, date, payment_type')
            ->get();

        $purchaseEfectivoDolares = Shopping::where('client_id', auth()->user()->headquarter->client_id)
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->where('status', 1)
            ->where('coin_id', 2)
            ->groupBy('date', 'payment_type', 'exchange_rate')
            ->where('payment_type', 'EFECTIVO')
            ->selectRaw('sum(total) as total, date, payment_type, exchange_rate')
            ->get();

        $purchaseDeposito = Shopping::where('client_id', auth()->user()->headquarter->client_id)
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->where('status', 1)
            ->where('coin_id', 1)
            ->groupBy('date', 'payment_type','bank_account_id')
            ->where('payment_type', 'DEPOSITO EN CUENTA')
            ->selectRaw('sum(total) as total, date, payment_type, bank_account_id, id')
            ->get();

        $purchaseDepositoDolares = Shopping::where('client_id', auth()->user()->headquarter->client_id)
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->where('status', 1)
            ->where('coin_id', 2)
            ->groupBy('date', 'payment_type','bank_account_id', 'exchange_rate')
            ->where('payment_type', 'DEPOSITO EN CUENTA')
            ->selectRaw('sum(total) as total, date, payment_type, bank_account_id, exchange_rate, id')
            ->get();

        $purchaseTarjeta = Shopping::where('client_id', auth()->user()->headquarter->client_id)
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->where('status', 1)
            ->where('coin_id', 1)
            ->where(function ($query) {
                $query->where('payment_type', 'TARJETA DE CREDITO')
                    ->orWhere('payment_type', 'TARJETA DE DEBITO');
            })
            ->groupBy('date', 'payment_type','payment_method_id')
            ->selectRaw('sum(total) as total, date, payment_type, payment_method_id, id')
            ->get();

        $purchaseTarjetaDolares = Shopping::where('client_id', auth()->user()->headquarter->client_id)
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->where('status', 1)
            ->where('coin_id', 2)
            ->where(function ($query) {
                $query->where('payment_type', 'TARJETA DE CREDITO')
                    ->orWhere('payment_type', 'TARJETA DE DEBITO');
            })
            ->groupBy('date', 'payment_type','payment_method_id', 'exchange_rate')
            ->selectRaw('sum(total) as total, date, payment_type, payment_method_id, exchange_rate, id')
            ->get();

        $purchasesCredit = Shopping::where('client_id', auth()->user()->headquarter->client_id)
            ->whereDate('date', '>=', $from)
            ->whereDate('date', '<=', $to)
            ->where('status', 1)
            ->where('payment_type', 'CREDITO')
            ->pluck('id');

        $credits = PurchaseCredit::where('client_id', auth()->user()->headquarter->client_id)
            ->whereIn('purchase_id', $purchasesCredit)
            ->pluck('id');

        $paymentsEfectivo = PurchaseCreditPayment::whereIn('purchase_credit_id', $credits)
            ->where('payment_type', 'EFECTIVO')
            ->whereBetween('date', [$from, $to])
            ->groupBy('date', 'cash_id','payment_type')
            ->selectRaw('sum(payment) as total, date, cash_id, payment_type')
            ->get();

        $paymentsDeposito = PurchaseCreditPayment::whereIn('purchase_credit_id', $credits)
            ->whereBetween('date', [$from, $to])
            ->where('payment_type', 'DEPOSITO EN CUENTA')
            ->groupBy('date', 'bank_account_id','payment_type')
            ->selectRaw('sum(payment) as total, date, bank_account_id, payment_type, id')
            ->get();

        $paymentsTarjeta = PurchaseCreditPayment::whereIn('purchase_credit_id', $credits)
            ->where('payment_type', 'TARJETA DE CREDITO')
            ->orWhere('payment_type', 'TARJETA DE DEBITO')
            ->whereBetween('date', [$from, $to])
            ->groupBy('date', 'payment_method_id','payment_type')
            ->selectRaw('sum(payment) as total, date, payment_method_id, payment_type, id')
            ->get();

        $ds = array();

        $c = 0;
        foreach ($purchaseEfectivo as $s ) {
            $ds[$c]['total'] = (float) $s->total;
            $ds[$c]['total_soles'] = (float) $s->total;
            $ds[$c]['total_dolares'] = "0";
            $ds[$c]['tc'] = "0";
            $ds[$c]['date'] = date('d-m-Y', strtotime($s->date));
            $ds[$c]['account'] = '1011101';
            $ds[$c]['method'] = $s->condition_payment;
            $ds[$c]['transaction'] = '008';
            $ds[$c]['glosa'] = "PAGO PROVEEDORES";
            $ds[$c]['coin'] = "038";
            $ds[$c]['type'] = 1;

            $shoppingDetails = Shopping::with('provider:id,document,description,code', 'coin:id,symbol', 'typeVoucher:id,code',
                'detail.product.centerCost', 'detail.centerCost')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->whereDate('date', date('Y-m-d', strtotime($s->date)))
                ->where('status', 1)
                ->where('coin_id', 1)
                ->where('payment_type', 'EFECTIVO')
                ->get();

            $cont = 0;

            foreach ($shoppingDetails as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->shopping_serie;
                $ds[$c]['details'][$cont]['correlative'] = $sd->shopping_correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
                $ds[$c]['details'][$cont]['total_soles'] = $sd->total;
                $ds[$c]['details'][$cont]['total_dolares'] = "000";
                $ds[$c]['details'][$cont]['tc'] = "000";
                $ds[$c]['details'][$cont]['method'] = $sd->condition_payment;
                $ds[$c]['details'][$cont]['othermethod'] = '';
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['coin'] = "038";
                $ds[$c]['details'][$cont]['opera'] = "SCV";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost != null ? $sd->detail[0]->product->centerCost->code : '';
                $cont++;
            }

            $c++;
        }

        foreach ($purchaseEfectivoDolares as $s) {
            $ds[$c]['total'] = (float) $s->total;
            $ds[$c]['total_soles'] = (float) $s->total * (float) $s->exchange_rate;
            $ds[$c]['total_dolares'] = (float) $s->total;
            $ds[$c]['tc'] = (float) $s->exchange_rate;
            $ds[$c]['date'] = date('d-m-Y', strtotime($s->date));
            $ds[$c]['account'] = '1011101';
            $ds[$c]['method'] = $s->condition_payment;
            $ds[$c]['transaction'] = '008';
            $ds[$c]['glosa'] = "PAGO PROVEEDORES";
            $ds[$c]['coin'] = "040";
            $ds[$c]['type'] = 1;

            $shoppingDetails = Shopping::with('provider:id,document,description,code', 'coin:id,symbol', 'typeVoucher:id,code',
                'detail.product.centerCost', 'detail.centerCost')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->whereDate('date', date('Y-m-d', strtotime($s->date)))
                ->where('status', 1)
                ->where('coin_id', 2)
                ->where('payment_type', 'EFECTIVO')
                ->get();

            $cont = 0;

            foreach ($shoppingDetails as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->shopping_serie;
                $ds[$c]['details'][$cont]['correlative'] = $sd->shopping_correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
                $ds[$c]['details'][$cont]['total_soles'] = $sd->total * $sd->exchange_rate;
                $ds[$c]['details'][$cont]['total_dolares'] = $sd->total;
                $ds[$c]['details'][$cont]['tc'] = $sd->exchange_rate;
                $ds[$c]['details'][$cont]['method'] = $sd->condition_payment;
                $ds[$c]['details'][$cont]['othermethod'] = '';
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['coin'] = "040";
                $ds[$c]['details'][$cont]['opera'] = "VEN";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost != null ? $sd->detail[0]->product->centerCost->code : '';
                $cont++;
            }

            $c++;
        }

        foreach ($purchaseDeposito as $s ) {
            $bank = BankAccount::where('client_id', auth()->user()->headquarter->client_id)
                ->find($s->bank_account_id);

            $ds[$c]['total'] = (float) $s->total;
            $ds[$c]['total_soles'] = (float) $s->total;
            $ds[$c]['total_dolares'] = "0";
            $ds[$c]['tc'] = "0";
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = $bank->accounting_account;
            $ds[$c]['method'] = $s->payment_type;
            $ds[$c]['transaction'] = '001';
            $ds[$c]['glosa'] = "PAGO DE PROVEEDORES";
            $ds[$c]['coin'] = "038";
            $ds[$c]['type'] = 1;

            $shoppingDetails = Shopping::with('provider:id,document,description,code', 'coin:id,symbol', 'typeVoucher:id,code',
                'detail.product.centerCost', 'detail.centerCost')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->whereDate('date', date('Y-m-d', strtotime($s->date)))
                ->where('status', 1)
                ->where('coin_id', 1)
                ->where('payment_type', $s->payment_type)
                ->get();

            $cont = 0;

            foreach ($shoppingDetails as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->shopping_serie;
                $ds[$c]['details'][$cont]['correlative'] = $sd->shopping_correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
                $ds[$c]['details'][$cont]['total_soles'] = $sd->total;
                $ds[$c]['details'][$cont]['total_dolares'] = "000";
                $ds[$c]['details'][$cont]['tc'] = "000";
                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
                $ds[$c]['details'][$cont]['othermethod'] = '';
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['coin'] = "038";
                $ds[$c]['details'][$cont]['opera'] = "SCV";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost == null ? '-' : $sd->detail[0]->product->centerCost->code;
                $cont++;
            }

            $c++;
        }

        foreach ($purchaseDepositoDolares as $s ) {
            $bank = BankAccount::where('client_id', auth()->user()->headquarter->client_id)
                ->find($s->bank_account_id);

            $ds[$c]['total'] = (float) $s->total;
            $ds[$c]['total_soles'] = (float) $s->total * (float) $s->exchange_rate;
            $ds[$c]['total_dolares'] = (float) $s->total;
            $ds[$c]['tc'] = (float) $s->exchange_rate;
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = $bank->accounting_account;
            $ds[$c]['method'] = $s->payment_type;
            $ds[$c]['transaction'] = '001';
            $ds[$c]['glosa'] = "PAGO DE PROVEEDORES";
            $ds[$c]['coin'] = "040";
            $ds[$c]['type'] = 1;

            $shoppingDetails = Shopping::with('provider:id,document,description,code', 'coin:id,symbol', 'typeVoucher:id,code',
                'detail.product.centerCost', 'detail.centerCost')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->whereDate('date', date('Y-m-d', strtotime($s->date)))
                ->where('status', 1)
                ->where('coin_id', 2)
                ->where('payment_type', $s->payment_type)
                ->get();

            $cont = 0;

            foreach ($shoppingDetails as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->shopping_serie;
                $ds[$c]['details'][$cont]['correlative'] = $sd->shopping_correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
                $ds[$c]['details'][$cont]['total_soles'] = $sd->total * $sd->exchange_rate;
                $ds[$c]['details'][$cont]['total_dolares'] = $sd->total;
                $ds[$c]['details'][$cont]['tc'] = $sd->exchange_rate;
                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
                $ds[$c]['details'][$cont]['othermethod'] = '';
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['coin'] = "040";
                $ds[$c]['details'][$cont]['opera'] = "VEN";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost == null ? '-' : $sd->detail[0]->product->centerCost->code;
                $cont++;
            }

            $c++;
        }

        foreach ($purchaseTarjeta as $s ) {
            $payment = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)
                ->find($s->payment_method_id);

            $ds[$c]['total'] = (float) $s->total;
            $ds[$c]['total_soles'] = (float) $s->total * (float) $s->exchange_rate;
            $ds[$c]['total_dolares'] = '0';
            $ds[$c]['tc'] = '0';
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = $payment == null ? '-' : $payment->account;
            $ds[$c]['method'] = $s->payment_type;
            if ($s->payment_type == 'TARJETA DE CREDITO') {
                $t = '006';
            } else {
                $t = '005';
            }
            $ds[$c]['transaction'] = $t;
            $ds[$c]['glosa'] = "PAGO DE PROVEEDORES";
            $ds[$c]['coin'] = "038";
            $ds[$c]['type'] = 1;

            $shoppingDetails = Shopping::with('provider:id,document,description,code', 'coin:id,symbol', 'typeVoucher:id,code',
                'detail.product.centerCost', 'detail.centerCost')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->whereDate('date', date('Y-m-d', strtotime($s->date)))
                ->where('status', 1)
                ->where('coin_id', 1)
                ->where('payment_type', $s->payment_type)
                ->get();

            if ($shoppingDetails->count() == 0) {
                $ds[$c]['details'] = [];
            }

            $cont = 0;

            foreach ($shoppingDetails as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->shopping_serie;
                $ds[$c]['details'][$cont]['correlative'] = $sd->shopping_correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
                $ds[$c]['details'][$cont]['total_soles'] = $sd->total;
                $ds[$c]['details'][$cont]['total_dolares'] = "000";
                $ds[$c]['details'][$cont]['tc'] = "000";
                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
                $ds[$c]['details'][$cont]['othermethod'] = '';
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['coin'] = "038";
                $ds[$c]['details'][$cont]['opera'] = "SCV";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost == null ? '-' : $sd->detail[0]->product->centerCost->code;
                $cont++;
            }

            $c++;
        }

        foreach ($purchaseTarjetaDolares as $s ) {
            $payment = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)
                ->find($s->payment_method_id);
            $ds[$c]['total'] = (float) $s->total;
            $ds[$c]['total_soles'] = (float) $s->total * (float) $s->exchange_rate;
            $ds[$c]['total_dolares'] = (float) $s->total;
            $ds[$c]['tc'] = (float) $s->exchange_rate;
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = $payment == null ? '-' : $payment->account;
            $ds[$c]['method'] = $s->payment_type;
            if ($s->payment_type == 'TARJETA DE CREDITO') {
                $t = '006';
            } else {
                $t = '005';
            }
            $ds[$c]['transaction'] = $t;
            $ds[$c]['glosa'] = "PAGO DE PROVEEDORES";
            $ds[$c]['coin'] = "040";
            $ds[$c]['type'] = 1;

            $shoppingDetails = Shopping::with('provider:id,document,description,code', 'coin:id,symbol', 'typeVoucher:id,code',
                'detail.product.centerCost', 'detail.centerCost')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->whereDate('date', date('Y-m-d', strtotime($s->date)))
                ->where('status', 1)
                ->where('coin_id', 2)
                ->where('payment_type', $s->payment_type)
                ->get();

            if ($shoppingDetails->count() == 0) {
                $ds[$c]['details'] = [];
            }

            $cont = 0;

            foreach ($shoppingDetails as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->shopping_serie;
                $ds[$c]['details'][$cont]['correlative'] = $sd->shopping_correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
                $ds[$c]['details'][$cont]['total_soles'] = $sd->total;
                $ds[$c]['details'][$cont]['total_dolares'] = $sd->total * $sd->exchange_rate;
                $ds[$c]['details'][$cont]['tc'] = $sd->exchange_rate;
                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
                $ds[$c]['details'][$cont]['othermethod'] = '';
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['coin'] = "040";
                $ds[$c]['details'][$cont]['opera'] = "VEN";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost == null ? '-' : $sd->detail[0]->product->centerCost->code;
                $cont++;
            }

            $c++;
        }

        foreach ($paymentsEfectivo as $s ) {
            $ds[$c]['total'] = (float) $s->total;
            $ds[$c]['total_soles'] = (float) $s->total;
            $ds[$c]['total_dolares'] = "";
            $ds[$c]['tc'] = "";
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = '1011101';
            $ds[$c]['method'] = $s->payment_type;
            $ds[$c]['transaction'] = '008';
            $ds[$c]['glosa'] = "PAGO DE PROVEEDORES";
            $ds[$c]['coin'] = "038";
            $ds[$c]['type'] = 2;



            $salesDetail = PurchaseCreditPayment::with('credit.shopping', 'credit.shopping.provider:id,document,description,code', 'credit.shopping.coin:id,symbol',
                'credit.shopping.typeVoucher:id,code', 'credit.shopping.detail.product.centerCost',
                'credit.shopping.detail.centerCost','bank', 'cash', 'paymentMethod')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->where('date', $s->date)
                ->where('payment_type', 'EFECTIVO')
                ->get();

            $cont = 0;

            foreach ($salesDetail as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->credit->shopping->shopping_serie;
                $ds[$c]['details'][$cont]['correlative'] = $sd->credit->shopping->shopping_correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->credit->shopping->provider->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->credit->shopping->provider->document;
                $ds[$c]['details'][$cont]['total_soles'] = $sd->payment;
                $ds[$c]['details'][$cont]['total_dolares'] = "000";
                $ds[$c]['details'][$cont]['tc'] = "000";
                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->credit->shopping->typeVoucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->credit->shopping->typeVoucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['coin'] = "038";
                $ds[$c]['details'][$cont]['opera'] = "SCV";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->credit->shopping->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->credit->shopping->detail[0]->product->centerCost->code;
                $cont++;
            }

            $c++;
        }

        foreach ($paymentsDeposito as $s ) {
            $bank = BankAccount::where('client_id', auth()->user()->headquarter->client_id)
                ->find($s->bank_account_id);

            $ds[$c]['total'] = (float) $s->total;
            $ds[$c]['total_soles'] = (float) $s->total;
            $ds[$c]['total_dolares'] = "";
            $ds[$c]['tc'] = "";
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = $bank->accounting_account;
            $ds[$c]['method'] = $s->payment_type;
            $ds[$c]['transaction'] = '001';
            $ds[$c]['glosa'] = "PAGO DE DOCUMENTOS  - DIA " . date('d', strtotime($s->date)) . "";
            $ds[$c]['coin'] = "038";
            $ds[$c]['type'] = 2;

            $salesDetail = PurchaseCreditPayment::with('credit.shopping', 'credit.shopping.provider:id,document,description,code', 'credit.shopping.coin:id,symbol',
                'credit.shopping.typeVoucher:id,code', 'credit.shopping.detail.product.centerCost',
                'credit.shopping.detail.centerCost','bank', 'cash', 'paymentMethod')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->where('date', $s->date)
                ->where('bank_account_id', $bank->id)
                ->where('payment_type', 'DEPOSITO EN CUENTA')
                ->get();

            $cont = 0;

            foreach ($salesDetail as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->credit->shopping->shopping_serie;
                $ds[$c]['details'][$cont]['correlative'] = $sd->credit->shopping->shopping_correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->credit->shopping->provider->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->credit->shopping->provider->document;
                $ds[$c]['details'][$cont]['total_soles'] = $sd->payment;
                $ds[$c]['details'][$cont]['total_dolares'] = "000";
                $ds[$c]['details'][$cont]['tc'] = "000";
                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->credit->shopping->typeVoucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->credit->shopping->typeVoucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
                $ds[$c]['details'][$cont]['expiration'] = "";
                $ds[$c]['details'][$cont]['coin'] = "038";
                $ds[$c]['details'][$cont]['opera'] = "SCV";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->credit->shopping->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->credit->shopping->detail[0]->product->centerCost == null ? "-" : $sd->credit->shopping->detail[0]->product->centerCost->code;
                $cont++;
            }

            $c++;
        }

        foreach ($paymentsTarjeta as $s ) {
            $payment = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)
                ->find($s->payment_method_id);

            $ds[$c]['total'] = (float) $s->total;
            $ds[$c]['total_soles'] = (float) $s->total;
            $ds[$c]['total_dolares'] = "";
            $ds[$c]['tc'] = "";
            $ds[$c]['date'] = $s->date;
            $ds[$c]['account'] = $payment->account;
            $ds[$c]['method'] = $s->payment_type;
            if ($s->payment_type == 'TARJETA DE CREDITO') {
                $t = '006';
            } else {
                $t = '005';
            }
            $ds[$c]['transaction'] = $t;
            $ds[$c]['glosa'] = "PAGO DE DOCUMENTOS  - DIA " . date('d', strtotime($s->date)) . "";
            $ds[$c]['coin'] = "038";
            $ds[$c]['type'] = 2;

            $salesDetail = PurchaseCreditPayment::with('credit.shopping', 'credit.shopping.provider:id,document,description,code', 'credit.shopping.coin:id,symbol',
                'credit.shopping.typeVoucher:id,code', 'credit.shopping.detail.product.centerCost',
                'credit.shopping.detail.centerCost','bank', 'cash', 'paymentMethod')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->where('date', $s->date)
                ->where('payment_type', 'TARJETA DE CREDITO')
                ->orWhere('payment_type', 'TARJETA DE DEBITO')
                ->get();

            $cont = 0;

            foreach ($salesDetail as $sd) {
                $ds[$c]['details'][$cont]['serie'] = $sd->credit->shopping->shopping_serie;
                $ds[$c]['details'][$cont]['correlative'] = $sd->credit->shopping->shopping_correlative;
                $ds[$c]['details'][$cont]['customer'] = $sd->credit->shopping->provider->code;
                $ds[$c]['details'][$cont]['customer_document'] = $sd->credit->shopping->provider->document;
                $ds[$c]['details'][$cont]['total_soles'] = $sd->payment;
                $ds[$c]['details'][$cont]['total_dolares'] = "";
                $ds[$c]['details'][$cont]['tc'] = "";
                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
                $ds[$c]['details'][$cont]['typeDocument'] = $sd->credit->shopping->typeVoucher->code;
                $ds[$c]['details'][$cont]['comprobante'] = $sd->credit->shopping->typeVoucher->description;
                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
                $ds[$c]['details'][$cont]['expiration'] = "-";
                $ds[$c]['details'][$cont]['coin'] = "038";
                $ds[$c]['details'][$cont]['opera'] = "SCV";
                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->credit->shopping->detail());
                $ds[$c]['details'][$cont]['center'] = $sd->credit->shopping->detail[0]->product->centerCost->code;
                $cont++;
            }

            $c++;
        }

        return Excel::download(new ProviderExpensesBookExport($purchaseEfectivo, $ds, $movements, $vourcher), 'INTERFAZ CUENTAS POR PAGAR.xlsx');
    }

//    public function generateFinancesPurchaseInterfaz(Request $request)
//    {
//        $movements = $request->movement;
//        $vourcher = $request->voucher;
//        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
//        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');
//
//        $dates = array();
//
//        $purchaseEfectivo = Shopping::where('client_id', auth()->user()->headquarter->client_id)
//                        ->whereBetween('date', [$from, $to])
//                        ->where('status', '!=', 9)
//                        ->where('coin_id', 1)
//                        ->where('type', 1)
//                        ->where('payment_type', 'EFECTIVO')
//                        ->selectRaw('sum(total) as total, date, payment_type')
//                        ->groupBy('date', 'payment_type')
//                        ->get();
//
//        $purchaseEfectivoDolares = Shopping::where('client_id', auth()->user()->headquarter->client_id)
//                        ->whereBetween('date', [$from, $to])
//                        ->where('status', '!=',9)
//                        ->where('coin_id', 2)
//                        ->where('type', 1)
//                        ->groupBy('date', 'payment_type', 'exchange_rate')
//                        ->where('payment_type', 'EFECTIVO')
//                        ->selectRaw('sum(total) as total, date, payment_type, exchange_rate')
//                        ->get();
//
//        $purchaseDeposito = Shopping::where('client_id', auth()->user()->headquarter->client_id)
//                            ->whereDate('date', '>=', $from)
//                            ->whereDate('date', '<=', $to)
//                            ->where('status', '!=', 9)
//                            ->where('coin_id', 1)
//                            ->where('type', 1)
//                            ->groupBy('date', 'payment_type','bank_account_id')
//                            ->where('payment_type', 'DEPOSITO EN CUENTA')
//                            ->selectRaw('sum(total) as total, date, payment_type, bank_account_id')
//                            ->get();
//
//        $purchaseDepositoDolares = Shopping::where('client_id', auth()->user()->headquarter->client_id)
//                            ->whereDate('date', '>=', $from)
//                            ->whereDate('date', '<=', $to)
//                            ->where('status', '!=', 9)
//                            ->where('coin_id', 2)
//                            ->where('type', 1)
//                            ->groupBy('date', 'payment_type','bank_account_id', 'exchange_rate')
//                            ->where('payment_type', 'DEPOSITO EN CUENTA')
//                            ->selectRaw('sum(total) as total, date, payment_type, bank_account_id, exchange_rate')
//                            ->get();
//
//        $purchaseTarjeta = Shopping::where('client_id', auth()->user()->headquarter->client_id)
//                            ->whereDate('date', '>=', $from)
//                            ->whereDate('date', '<=', $to)
//                            ->where('status', '!=', 9)
//                            ->where('coin_id', 1)
//                            ->where(function ($query) {
//                                $query->where('payment_type', 'TARJETA DE CREDITO')
//                                        ->orWhere('payment_type', 'TARJETA DE DEBITO');
//                            })
//                            ->where('type', 1)
//                            ->groupBy('date', 'payment_type','payment_method_id')
//                            ->selectRaw('sum(total) as total, date, payment_type, payment_method_id')
//                            ->get();
//
//        $purchaseTarjetaDolares = Shopping::where('client_id', auth()->user()->headquarter->client_id)
//                            ->whereDate('date', '>=', $from)
//                            ->whereDate('date', '<=', $to)
//                            ->where('status', '!=', 9)
//                            ->where('coin_id', 2)
//                            ->where(function ($query) {
//                                $query->where('payment_type', 'TARJETA DE CREDITO')
//                                        ->orWhere('payment_type', 'TARJETA DE DEBITO');
//                            })
//                            ->where('type', 1)
//                            ->groupBy('date', 'payment_type','payment_method_id', 'exchange_rate')
//                            ->selectRaw('sum(total) as total, date, payment_type, payment_method_id, exchange_rate')
//                            ->get();
//
//        $purchasesCredit = Shopping::where('client_id', auth()->user()->headquarter->client_id)
//                            ->whereDate('date', '>=', $from)
//                            ->whereDate('date', '<=', $to)
//                            ->where('status', '!=', 9)
//                            ->where('type', 1)
//                            ->where('payment_type', 'CREDITO')
//                            ->pluck('id');
//
//        $credits = PurchaseCredit::where('client_id', auth()->user()->headquarter->client_id)
//                            ->whereIn('purchase_id', $purchasesCredit)
//                            ->pluck('id');
//
//        $paymentsEfectivo = PurchaseCreditPayment::whereIn('purchase_credit_id', $credits)
//                                    ->where('payment_type', 'EFECTIVO')
//                                    ->whereBetween('date', [$from, $to])
//                                    ->groupBy('date', 'cash_id','payment_type')
//                                    ->selectRaw('sum(payment) as total, date, cash_id, payment_type')
//                                    ->get();
//
//        $paymentsDeposito = PurchaseCreditPayment::whereIn('purchase_credit_id', $credits)
//                                    ->whereBetween('date', [$from, $to])
//                                    ->where('payment_type', 'DEPOSITO EN CUENTA')
//                                    ->groupBy('date', 'bank_account_id','payment_type')
//                                    ->selectRaw('sum(payment) as total, date, bank_account_id, payment_type')
//                                    ->get();
//
//        $paymentsTarjeta = PurchaseCreditPayment::whereIn('purchase_credit_id', $credits)
//                                ->where('payment_type', 'TARJETA DE CREDITO')
//                                ->orWhere('payment_type', 'TARJETA DE DEBITO')
//                                ->whereBetween('date', [$from, $to])
//                                ->groupBy('date', 'payment_method_id','payment_type')
//                                ->selectRaw('sum(payment) as total, date, payment_method_id, payment_type')
//                                ->get();
//
//        $ds = array();
//
//        $c = 0;
//        foreach ($purchaseEfectivo as $s ) {
//            $ds[$c]['total'] = (float) $s->total;
//            $ds[$c]['total_soles'] = (float) $s->total;
//            $ds[$c]['total_dolares'] = "0";
//            $ds[$c]['tc'] = "0";
//            $ds[$c]['date'] = date('d-m-Y', strtotime($s->date));
//            $ds[$c]['account'] = '1011101';
//            $ds[$c]['method'] = $s->condition_payment;
//            $ds[$c]['transaction'] = '008';
//            $ds[$c]['glosa'] = "PAGO PROVEEDORES";
//            $ds[$c]['coin'] = "038";
//            $ds[$c]['type'] = 1;
//
//            $shoppingDetails = Shopping::with('provider:id,document,description,code', 'coin:id,symbol', 'typeVoucher:id,code',
//                                    'detail.product.centerCost', 'detail.centerCost')
//                        ->where('client_id', auth()->user()->headquarter->client_id)
//                        ->whereDate('date', date('Y-m-d', strtotime($s->date)))
//                        ->where('status', '!=',9)
//                        ->where('coin_id', 1)
//                        ->where('type', 1)
//                        ->where('payment_type', 'EFECTIVO')
//                        ->get();
//
//            $cont = 0;
//
//            foreach ($shoppingDetails as $sd) {
//                $ds[$c]['details'][$cont]['serie'] = $sd->shopping_serie;
//                $ds[$c]['details'][$cont]['correlative'] = $sd->shopping_correlative;
//                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
//                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
//                $ds[$c]['details'][$cont]['total_soles'] = $sd->total;
//                $ds[$c]['details'][$cont]['total_dolares'] = "000";
//                $ds[$c]['details'][$cont]['tc'] = "000";
//                $ds[$c]['details'][$cont]['method'] = $sd->condition_payment;
//                $ds[$c]['details'][$cont]['othermethod'] = '';
//                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
//                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
//                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
//                $ds[$c]['details'][$cont]['expiration'] = "";
//                $ds[$c]['details'][$cont]['coin'] = "038";
//                $ds[$c]['details'][$cont]['opera'] = "SCV";
//                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
//                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost != null ? $sd->detail[0]->product->centerCost->code : '';
//                $cont++;
//            }
//
//            $c++;
//        }
//
//        foreach ($purchaseEfectivoDolares as $s) {
//            $ds[$c]['total'] = (float) $s->total;
//            $ds[$c]['total_soles'] = (float) $s->total * (float) $s->exchange_rate;
//            $ds[$c]['total_dolares'] = (float) $s->total;
//            $ds[$c]['tc'] = (float) $s->exchange_rate;
//            $ds[$c]['date'] = date('d-m-Y', strtotime($s->date));
//            $ds[$c]['account'] = '1011101';
//            $ds[$c]['method'] = $s->condition_payment;
//            $ds[$c]['transaction'] = '008';
//            $ds[$c]['glosa'] = "PAGO PROVEEDORES";
//            $ds[$c]['coin'] = "040";
//            $ds[$c]['type'] = 1;
//
//            $shoppingDetails = Shopping::with('provider:id,document,description,code', 'coin:id,symbol', 'typeVoucher:id,code',
//                                    'detail.product.centerCost', 'detail.centerCost')
//                        ->where('client_id', auth()->user()->headquarter->client_id)
//                        ->whereDate('date', date('Y-m-d', strtotime($s->date)))
//                        ->where('status', '!=',9)
//                        ->where('coin_id', 2)
//                        ->where('type', 1)
//                        ->where('payment_type', 'EFECTIVO')
//                        ->get();
//
//            $cont = 0;
//
//            foreach ($shoppingDetails as $sd) {
//                $ds[$c]['details'][$cont]['serie'] = $sd->shopping_serie;
//                $ds[$c]['details'][$cont]['correlative'] = $sd->shopping_correlative;
//                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
//                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
//                $ds[$c]['details'][$cont]['total_soles'] = $sd->total * $sd->exchange_rate;
//                $ds[$c]['details'][$cont]['total_dolares'] = $sd->total;
//                $ds[$c]['details'][$cont]['tc'] = $sd->exchange_rate;
//                $ds[$c]['details'][$cont]['method'] = $sd->condition_payment;
//                $ds[$c]['details'][$cont]['othermethod'] = '';
//                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
//                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
//                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
//                $ds[$c]['details'][$cont]['expiration'] = "";
//                $ds[$c]['details'][$cont]['coin'] = "040";
//                $ds[$c]['details'][$cont]['opera'] = "VEN";
//                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
//                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost != null ? $sd->detail[0]->product->centerCost->code : '';
//                $cont++;
//            }
//
//            $c++;
//        }
//
//        foreach ($purchaseDeposito as $s ) {
//            $bank = BankAccount::where('client_id', auth()->user()->headquarter->client_id)
//                                ->find($s->bank_account_id);
//
//            $glosa = "PAGO PROVEEDORES";
//
//            $ds[$c]['total'] = (float) $s->total;
//            $ds[$c]['total_soles'] = (float) $s->total;
//            $ds[$c]['total_dolares'] = "0";
//            $ds[$c]['tc'] = "0";
//            $ds[$c]['date'] = $s->date;
//            $ds[$c]['account'] = $bank->accounting_account;
//            $ds[$c]['method'] = $s->payment_type;
//            $ds[$c]['transaction'] = '001';
//            $ds[$c]['glosa'] = $glosa;
//            $ds[$c]['coin'] = "038";
//            $ds[$c]['type'] = 1;
//
//            $shoppingDetails = Shopping::with('provider:id,document,description,code', 'coin:id,symbol', 'typeVoucher:id,code',
//                                                'detail.product.centerCost', 'detail.centerCost', 'bankMovement', 'bankAccount')
//                                        ->where('client_id', auth()->user()->headquarter->client_id)
//                                        ->whereDate('date', date('Y-m-d', strtotime($s->date)))
//                                        ->where('status', '!=',9)
//                                        ->where('coin_id', 1)
//                                        ->where('type', 1)
//                                        ->where('payment_type', $s->payment_type)
//                                        ->get();
//
//            $cont = 0;
//            $ds[$c]['details'] = [];
//            foreach ($shoppingDetails as $sd) {
//                $isItf = false;
//                $glosa = "PAGO - {$sd->provider->description}";
//
////                if ($sd->bankMovement) {
////                    if (Str::upper($sd->bankMovement->description) == 'ITF' ||
////                        Str::upper($sd->bankMovement->description) == 'IMPUESTO ITF') {
////                        $isItf = false;
////                        $glosa = "ITF - {$sd->bankAccount->bank_name}";
////                    }
////                }
////
////                $ds[$c]['details'][$cont]['bank_account'] = $bank->accounting_account;
////                $ds[$c]['details'][$cont]['glosa'] = $glosa;
////                $ds[$c]['details'][$cont]['serie'] = "000";
////                $ds[$c]['details'][$cont]['correlative'] = $sd->bankMovement->operation_number;
////                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
////                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
////                $ds[$c]['details'][$cont]['total_soles'] = $sd->bankMovement->amount;
////                $ds[$c]['details'][$cont]['total_dolares'] = "000";
////                $ds[$c]['details'][$cont]['tc'] = "000";
////                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
////                $ds[$c]['details'][$cont]['othermethod'] = '';
////                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
////                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
////                $ds[$c]['details'][$cont]['paymentAccount'] =  $isItf ? "6412101" : '4211012';
////                $ds[$c]['details'][$cont]['expiration'] = "";
////                $ds[$c]['details'][$cont]['coin'] = "038";
////                $ds[$c]['details'][$cont]['opera'] = "SCV";
////                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
////                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost == null ? '-' : $sd->detail[0]->product->centerCost->code;
//
////                $cont++;
//
//                $ds[$c]['details'][$cont]['account'] = $isItf ? "6412101" : $bank->accounting_account;
//                $ds[$c]['details'][$cont]['glosa'] = $glosa;
//                $ds[$c]['details'][$cont]['serie'] = $sd->shopping_serie;
//                $ds[$c]['details'][$cont]['correlative'] = $sd->shopping_correlative;
//                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
//                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
//                $ds[$c]['details'][$cont]['total_soles'] = $sd->total;
//                $ds[$c]['details'][$cont]['total_dolares'] = "000";
//                $ds[$c]['details'][$cont]['tc'] = "000";
//                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
//                $ds[$c]['details'][$cont]['othermethod'] = '';
//                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
//                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
//                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
//                $ds[$c]['details'][$cont]['expiration'] = "";
//                $ds[$c]['details'][$cont]['cobro'] = $isItf ? '' : 'C';
//                $ds[$c]['details'][$cont]['coin'] = "038";
//                $ds[$c]['details'][$cont]['opera'] = "SCV";
//                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
//                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost == null ? '-' : $sd->detail[0]->product->centerCost->code;
//                $cont++;
//            }
//
//            $c++;
//        }
//
//        foreach ($purchaseDepositoDolares as $s ) {
//            $bank = BankAccount::where('client_id', auth()->user()->headquarter->client_id)
//                                ->find($s->bank_account_id);
//
//            $ds[$c]['total'] = (float) $s->total;
//            $ds[$c]['total_soles'] = (float) $s->total * (float) $s->exchange_rate;
//            $ds[$c]['total_dolares'] = (float) $s->total;
//            $ds[$c]['tc'] = (float) $s->exchange_rate;
//            $ds[$c]['date'] = $s->date;
//            $ds[$c]['account'] = $bank->accounting_account;
//            $ds[$c]['method'] = $s->payment_type;
//            $ds[$c]['transaction'] = '001';
//            $ds[$c]['glosa'] = "PAGO DE PROVEEDORES";
//            $ds[$c]['coin'] = "040";
//            $ds[$c]['type'] = 1;
//
//            $shoppingDetails = Shopping::with('provider:id,document,description,code', 'coin:id,symbol', 'typeVoucher:id,code',
//                                                'detail.product.centerCost', 'detail.centerCost')
//                                        ->where('client_id', auth()->user()->headquarter->client_id)
//                                        ->whereDate('date', date('Y-m-d', strtotime($s->date)))
//                                        ->where('status', '!=',9)
//                                        ->where('coin_id', 2)
//                                        ->where('type', 1)
//                                        ->where('payment_type', $s->payment_type)
//                                        ->get();
//
//            $cont = 0;
//
//            foreach ($shoppingDetails as $sd) {
//                $ds[$c]['details'][$cont]['serie'] = $sd->shopping_serie;
//                $ds[$c]['details'][$cont]['correlative'] = $sd->shopping_correlative;
//                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
//                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
//                $ds[$c]['details'][$cont]['total_soles'] = $sd->total * $sd->exchange_rate;
//                $ds[$c]['details'][$cont]['total_dolares'] = $sd->total;
//                $ds[$c]['details'][$cont]['tc'] = $sd->exchange_rate;
//                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
//                $ds[$c]['details'][$cont]['othermethod'] = '';
//                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
//                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
//                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
//                $ds[$c]['details'][$cont]['expiration'] = "";
//                $ds[$c]['details'][$cont]['coin'] = "040";
//                $ds[$c]['details'][$cont]['opera'] = "VEN";
//                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
//                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost == null ? '-' : $sd->detail[0]->product->centerCost->code;
//                $cont++;
//            }
//
//            $c++;
//        }
//
//        foreach ($purchaseTarjeta as $s ) {
//            $payment = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)
//                                ->find($s->payment_method_id);
//
//            $ds[$c]['total'] = (float) $s->total;
//            $ds[$c]['total_soles'] = (float) $s->total * (float) $s->exchange_rate;
//            $ds[$c]['total_dolares'] = '0';
//            $ds[$c]['tc'] = '0';
//            $ds[$c]['date'] = $s->date;
//            $ds[$c]['account'] = $payment == null ? '-' : $payment->account;
//            $ds[$c]['method'] = $s->payment_type;
//            if ($s->payment_type == 'TARJETA DE CREDITO') {
//                $t = '006';
//            } else {
//                $t = '005';
//            }
//            $ds[$c]['transaction'] = $t;
//            $ds[$c]['glosa'] = "PAGO DE PROVEEDORES";
//            $ds[$c]['coin'] = "038";
//            $ds[$c]['type'] = 1;
//
//            $shoppingDetails = Shopping::with('provider:id,document,description,code', 'coin:id,symbol', 'typeVoucher:id,code',
//                                                'detail.product.centerCost', 'detail.centerCost')
//                                        ->where('client_id', auth()->user()->headquarter->client_id)
//                                        ->whereDate('date', date('Y-m-d', strtotime($s->date)))
//                                        ->where('status', '!=',9)
//                                        ->where('coin_id', 1)
//                                        ->where('type', 1)
//                                        ->where('payment_type', $s->payment_type)
//                                        ->get();
//
//            if ($shoppingDetails->count() == 0) {
//                $ds[$c]['details'] = [];
//            }
//
//            $cont = 0;
//
//            foreach ($shoppingDetails as $sd) {
//                $ds[$c]['details'][$cont]['serie'] = $sd->shopping_serie;
//                $ds[$c]['details'][$cont]['correlative'] = $sd->shopping_correlative;
//                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
//                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
//                $ds[$c]['details'][$cont]['total_soles'] = $sd->total;
//                $ds[$c]['details'][$cont]['total_dolares'] = "000";
//                $ds[$c]['details'][$cont]['tc'] = "000";
//                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
//                $ds[$c]['details'][$cont]['othermethod'] = '';
//                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
//                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
//                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
//                $ds[$c]['details'][$cont]['expiration'] = "";
//                $ds[$c]['details'][$cont]['coin'] = "038";
//                $ds[$c]['details'][$cont]['opera'] = "SCV";
//                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
//                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost == null ? '-' : $sd->detail[0]->product->centerCost->code;
//                $cont++;
//            }
//
//            $c++;
//        }
//
//        foreach ($purchaseTarjetaDolares as $s ) {
//            $payment = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)
//                                ->find($s->payment_method_id);
//            $ds[$c]['total'] = (float) $s->total;
//            $ds[$c]['total_soles'] = (float) $s->total * (float) $s->exchange_rate;
//            $ds[$c]['total_dolares'] = (float) $s->total;
//            $ds[$c]['tc'] = (float) $s->exchange_rate;
//            $ds[$c]['date'] = $s->date;
//            $ds[$c]['account'] = $payment == null ? '-' : $payment->account;
//            $ds[$c]['method'] = $s->payment_type;
//            if ($s->payment_type == 'TARJETA DE CREDITO') {
//                $t = '006';
//            } else {
//                $t = '005';
//            }
//            $ds[$c]['transaction'] = $t;
//            $ds[$c]['glosa'] = "PAGO DE PROVEEDORES";
//            $ds[$c]['coin'] = "040";
//            $ds[$c]['type'] = 1;
//
//            $shoppingDetails = Shopping::with('provider:id,document,description,code', 'coin:id,symbol', 'typeVoucher:id,code',
//                                                'detail.product.centerCost', 'detail.centerCost')
//                                        ->where('client_id', auth()->user()->headquarter->client_id)
//                                        ->whereDate('date', date('Y-m-d', strtotime($s->date)))
//                                        ->where('status', '!=',9)
//                                        ->where('coin_id', 2)
//                                        ->where('type', 1)
//                                        ->where('payment_type', $s->payment_type)
//                                        ->get();
//
//            if ($shoppingDetails->count() == 0) {
//                $ds[$c]['details'] = [];
//            }
//
//            $cont = 0;
//
//            foreach ($shoppingDetails as $sd) {
//                $ds[$c]['details'][$cont]['serie'] = $sd->shopping_serie;
//                $ds[$c]['details'][$cont]['correlative'] = $sd->shopping_correlative;
//                $ds[$c]['details'][$cont]['customer'] = $sd->provider->code;
//                $ds[$c]['details'][$cont]['customer_document'] = $sd->provider->document;
//                $ds[$c]['details'][$cont]['total_soles'] = $sd->total;
//                $ds[$c]['details'][$cont]['total_dolares'] = $sd->total * $sd->exchange_rate;
//                $ds[$c]['details'][$cont]['tc'] = $sd->exchange_rate;
//                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
//                $ds[$c]['details'][$cont]['othermethod'] = '';
//                $ds[$c]['details'][$cont]['typeDocument'] = $sd->typeVoucher->code;
//                $ds[$c]['details'][$cont]['comprobante'] = $sd->typeVoucher->description;
//                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
//                $ds[$c]['details'][$cont]['expiration'] = "";
//                $ds[$c]['details'][$cont]['coin'] = "040";
//                $ds[$c]['details'][$cont]['opera'] = "VEN";
//                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->detail());
//                $ds[$c]['details'][$cont]['center'] = $sd->detail[0]->product->centerCost == null ? '-' : $sd->detail[0]->product->centerCost->code;
//                $cont++;
//            }
//
//            $c++;
//        }
//
//        foreach ($paymentsEfectivo as $s ) {
//            $ds[$c]['total'] = (float) $s->total;
//            $ds[$c]['total_soles'] = (float) $s->total;
//            $ds[$c]['total_dolares'] = "";
//            $ds[$c]['tc'] = "";
//            $ds[$c]['date'] = $s->date;
//            $ds[$c]['account'] = '1011101';
//            $ds[$c]['method'] = $s->payment_type;
//            $ds[$c]['transaction'] = '008';
//            $ds[$c]['glosa'] = "PAGO DE PROVEEDORES";
//            $ds[$c]['coin'] = "038";
//            $ds[$c]['type'] = 2;
//
//
//
//            $salesDetail = PurchaseCreditPayment::with('credit.shopping', 'credit.shopping.provider:id,document,description,code', 'credit.shopping.coin:id,symbol',
//                                                'credit.shopping.typeVoucher:id,code', 'credit.shopping.detail.product.centerCost',
//                                                'credit.shopping.detail.centerCost','bank', 'cash', 'paymentMethod')
//                                ->where('client_id', auth()->user()->headquarter->client_id)
//                                ->where('date', $s->date)
//                                ->where('payment_type', 'EFECTIVO')
//                                ->get();
//
//            $cont = 0;
//
//            foreach ($salesDetail as $sd) {
//                $ds[$c]['details'][$cont]['serie'] = $sd->credit->shopping->shopping_serie;
//                $ds[$c]['details'][$cont]['correlative'] = $sd->credit->shopping->shopping_correlative;
//                $ds[$c]['details'][$cont]['customer'] = $sd->credit->shopping->provider->code;
//                $ds[$c]['details'][$cont]['customer_document'] = $sd->credit->shopping->provider->document;
//                $ds[$c]['details'][$cont]['total_soles'] = $sd->payment;
//                $ds[$c]['details'][$cont]['total_dolares'] = "000";
//                $ds[$c]['details'][$cont]['tc'] = "000";
//                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
//                $ds[$c]['details'][$cont]['typeDocument'] = $sd->credit->shopping->typeVoucher->code;
//                $ds[$c]['details'][$cont]['comprobante'] = $sd->credit->shopping->typeVoucher->description;
//                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
//                $ds[$c]['details'][$cont]['expiration'] = "";
//                $ds[$c]['details'][$cont]['coin'] = "038";
//                $ds[$c]['details'][$cont]['opera'] = "SCV";
//                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->credit->shopping->detail());
//                $ds[$c]['details'][$cont]['center'] = $sd->credit->shopping->detail[0]->product->centerCost->code;
//                $cont++;
//            }
//
//            $c++;
//        }
//
//        foreach ($paymentsDeposito as $s ) {
//            $bank = BankAccount::where('client_id', auth()->user()->headquarter->client_id)
//                                ->find($s->bank_account_id);
//
//            $ds[$c]['total'] = (float) $s->total;
//            $ds[$c]['total_soles'] = (float) $s->total;
//            $ds[$c]['total_dolares'] = "";
//            $ds[$c]['tc'] = "";
//            $ds[$c]['date'] = $s->date;
//            $ds[$c]['account'] = $bank->accounting_account;
//            $ds[$c]['method'] = $s->payment_type;
//            $ds[$c]['transaction'] = '001';
//            $ds[$c]['glosa'] = "PAGO DE DOCUMENTOS  - DIA " . date('d', strtotime($s->date)) . "";
//            $ds[$c]['coin'] = "038";
//            $ds[$c]['type'] = 2;
//
//            $salesDetail = PurchaseCreditPayment::with('credit.shopping', 'credit.shopping.provider:id,document,description,code', 'credit.shopping.coin:id,symbol',
//                                        'credit.shopping.typeVoucher:id,code', 'credit.shopping.detail.product.centerCost',
//                                        'credit.shopping.detail.centerCost','bank', 'cash', 'paymentMethod')
//                                ->where('client_id', auth()->user()->headquarter->client_id)
//                                ->where('date', $s->date)
//                                ->where('bank_account_id', $bank->id)
//                                ->where('payment_type', 'DEPOSITO EN CUENTA')
//                                ->get();
//
//            $cont = 0;
//
//            foreach ($salesDetail as $sd) {
//                $ds[$c]['details'][$cont]['serie'] = $sd->credit->shopping->shopping_serie;
//                $ds[$c]['details'][$cont]['correlative'] = $sd->credit->shopping->shopping_correlative;
//                $ds[$c]['details'][$cont]['customer'] = $sd->credit->shopping->provider->code;
//                $ds[$c]['details'][$cont]['customer_document'] = $sd->credit->shopping->provider->document;
//                $ds[$c]['details'][$cont]['total_soles'] = $sd->payment;
//                $ds[$c]['details'][$cont]['total_dolares'] = "000";
//                $ds[$c]['details'][$cont]['tc'] = "000";
//                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
//                $ds[$c]['details'][$cont]['typeDocument'] = $sd->credit->shopping->typeVoucher->code;
//                $ds[$c]['details'][$cont]['comprobante'] = $sd->credit->shopping->typeVoucher->description;
//                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
//                $ds[$c]['details'][$cont]['expiration'] = "";
//                $ds[$c]['details'][$cont]['coin'] = "038";
//                $ds[$c]['details'][$cont]['opera'] = "SCV";
//                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->credit->shopping->detail());
//                $ds[$c]['details'][$cont]['center'] = $sd->credit->shopping->detail[0]->product->centerCost == null ? "-" : $sd->credit->shopping->detail[0]->product->centerCost->code;
//                $cont++;
//            }
//
//            $c++;
//        }
//
//        foreach ($paymentsTarjeta as $s ) {
//            $payment = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)
//                                ->find($s->payment_method_id);
//
//            $ds[$c]['total'] = (float) $s->total;
//            $ds[$c]['total_soles'] = (float) $s->total;
//            $ds[$c]['total_dolares'] = "";
//            $ds[$c]['tc'] = "";
//            $ds[$c]['date'] = $s->date;
//            $ds[$c]['account'] = $payment->account;
//            $ds[$c]['method'] = $s->payment_type;
//            if ($s->payment_type == 'TARJETA DE CREDITO') {
//                $t = '006';
//            } else {
//                $t = '005';
//            }
//            $ds[$c]['transaction'] = $t;
//            $ds[$c]['glosa'] = "PAGO DE DOCUMENTOS  - DIA " . date('d', strtotime($s->date)) . "";
//            $ds[$c]['coin'] = "038";
//            $ds[$c]['type'] = 2;
//
//            $salesDetail = PurchaseCreditPayment::with('credit.shopping', 'credit.shopping.provider:id,document,description,code', 'credit.shopping.coin:id,symbol',
//                                    'credit.shopping.typeVoucher:id,code', 'credit.shopping.detail.product.centerCost',
//                                    'credit.shopping.detail.centerCost','bank', 'cash', 'paymentMethod')
//                                ->where('client_id', auth()->user()->headquarter->client_id)
//                                ->where('date', $s->date)
//                                ->where('payment_type', 'TARJETA DE CREDITO')
//                                ->orWhere('payment_type', 'TARJETA DE DEBITO')
//                                ->get();
//
//            $cont = 0;
//
//            foreach ($salesDetail as $sd) {
//                $ds[$c]['details'][$cont]['serie'] = $sd->credit->shopping->shopping_serie;
//                $ds[$c]['details'][$cont]['correlative'] = $sd->credit->shopping->shopping_correlative;
//                $ds[$c]['details'][$cont]['customer'] = $sd->credit->shopping->provider->code;
//                $ds[$c]['details'][$cont]['customer_document'] = $sd->credit->shopping->provider->document;
//                $ds[$c]['details'][$cont]['total_soles'] = $sd->payment;
//                $ds[$c]['details'][$cont]['total_dolares'] = "";
//                $ds[$c]['details'][$cont]['tc'] = "";
//                $ds[$c]['details'][$cont]['method'] = $sd->payment_type;
//                $ds[$c]['details'][$cont]['typeDocument'] = $sd->credit->shopping->typeVoucher->code;
//                $ds[$c]['details'][$cont]['comprobante'] = $sd->credit->shopping->typeVoucher->description;
//                $ds[$c]['details'][$cont]['paymentAccount'] = '4211012';
//                $ds[$c]['details'][$cont]['expiration'] = "-";
//                $ds[$c]['details'][$cont]['coin'] = "038";
//                $ds[$c]['details'][$cont]['opera'] = "SCV";
//                $ds[$c]['details'][$cont]['cos_codigo'] = $this->getCosCodigo($sd->credit->shopping->detail());
//                $ds[$c]['details'][$cont]['center'] = $sd->credit->shopping->detail[0]->product->centerCost->code;
//                $cont++;
//            }
//
//            $c++;
//        }
//
//        return Excel::download(new ProviderExpensesBookExport($purchaseEfectivo, $ds, $movements, $vourcher), 'INTERFAZ CUENTAS POR PAGAR.xlsx');
//    }

    public function getCosCodigo($details) {
        $detail = $details->orderBy('total', 'desc')->get();
        return $detail[0]->product->stock && $detail[0]->product->stock->warehouse ? $detail[0]->product->stock->warehouse->code : '';
    }

    public function configuration()
    {
        $ap = AccountingAccount::where('type', 1)->where('client_id', auth()->user()->headquarter->client_id)->select('account')->first();
        $ai = AccountingAccount::where('type', 2)->where('client_id', auth()->user()->headquarter->client_id)->select('account')->first();
        $ac = AccountingAccount::where('type', 3)->where('client_id', auth()->user()->headquarter->client_id)->select('account')->first();
        $ab = AccountingAccount::where('type', 4)->where('client_id', auth()->user()->headquarter->client_id)->select('account')->first();
        $ahonorarios = AccountingAccount::where('type', 5)->where('client_id', auth()->user()->headquarter->client_id)->select('account')->first();
        $aretencion = AccountingAccount::where('type', 6)->where('client_id', auth()->user()->headquarter->client_id)->select('account')->first();
        $arelsales = AccountingAccount::where('type', 7)->where('client_id', auth()->user()->headquarter->client_id)->select('account')->first();
        $arelpurchases = AccountingAccount::where('type', 8)->where('client_id', auth()->user()->headquarter->client_id)->select('account')->first();

        if ($ap == null) {
            $ap = null;
        } else {
            $ap = $ap->account;
        }

        if ($ai == null) {
            $ai = null;
        } else {
            $ai = $ai->account;
        }
        
        if ($ac == null) {
            $ac = null;
        } else {
            $ac = $ac->account;
        }
        
        if ($ab == null) {
            $ab = null;
        } else {
            $ab = $ab->account;
        }

        if ($ahonorarios == null) {
            $ahonorarios = null;
        } else {
            $ahonorarios = $ahonorarios->account;
        }

        if ($aretencion == null) {
            $aretencion = null;
        } else {
            $aretencion = $aretencion->account;
        }

        if ($arelsales == null) {
            $arelsales = null;
        } else {
            $arelsales = $arelsales->account;
        }

        if ($arelpurchases == null) {
            $arelpurchases = null;
        } else {
            $arelpurchases = $arelpurchases->account;
        }

        return view('accountancy.configuration', compact('ap','ai', 'ac','ab', 'ahonorarios', 'aretencion', 'arelpurchases', 'arelsales'));
    }

    public function configurationStore(Request $request)
    {
        $ac = AccountingAccount::where('client_id', auth()->user()->headquarter->client_id)->where('type', 1)->first();

        if ($ac == null) {
            $ac = new AccountingAccount;
            $ac->type = 1;
            $ac->client_id = auth()->user()->headquarter->client_id;
        }

        $ac->account = $request->cta_providers;
        $ac->save();


        $ac2 = AccountingAccount::where('client_id', auth()->user()->headquarter->client_id)->where('type', 2)->first();

        if ($ac2 == null) {
            $ac2 = new AccountingAccount;
            $ac2->type = 2;
            $ac2->client_id = auth()->user()->headquarter->client_id;
        }

        $ac2->account = $request->cta_igv;
        $ac2->save();
        
        $ac2 = AccountingAccount::where('client_id', auth()->user()->headquarter->client_id)->where('type', 3)->first();

        if ($ac2 == null) {
            $ac2 = new AccountingAccount;
            $ac2->type = 3;
            $ac2->client_id = auth()->user()->headquarter->client_id;
        }

        $ac2->account = $request->cta_recharge;
        $ac2->save();

        $ac2 = AccountingAccount::where('client_id', auth()->user()->headquarter->client_id)->where('type', 4)->first();

        if ($ac2 == null) {
            $ac2 = new AccountingAccount;
            $ac2->type = 4;
            $ac2->client_id = auth()->user()->headquarter->client_id;
        }

        $ac2->account = $request->cta_icbper;
        $ac2->save();

        $ac2 = AccountingAccount::where('client_id', auth()->user()->headquarter->client_id)->where('type', 5)->first();

        if ($ac2 == null) {
            $ac2 = new AccountingAccount;
            $ac2->type = 5;
            $ac2->client_id = auth()->user()->headquarter->client_id;
        }

        $ac2->account = $request->cta_honorarios;
        $ac2->save();

        $ac2 = AccountingAccount::where('client_id', auth()->user()->headquarter->client_id)->where('type', 6)->first();

        if ($ac2 == null) {
            $ac2 = new AccountingAccount;
            $ac2->type = 6;
            $ac2->client_id = auth()->user()->headquarter->client_id;
        }

        $ac2->account = $request->cta_retencion;
        $ac2->save();

        $ac2 = AccountingAccount::where('client_id', auth()->user()->headquarter->client_id)->where('type', 7)->first();

        if ($ac2 == null) {
            $ac2 = new AccountingAccount;
            $ac2->type = 7;
            $ac2->client_id = auth()->user()->headquarter->client_id;
        }

        $ac2->account = $request->cta_rel_sales;
        $ac2->save();

        $ac2 = AccountingAccount::where('client_id', auth()->user()->headquarter->client_id)->where('type', 8)->first();

        if ($ac2 == null) {
            $ac2 = new AccountingAccount;
            $ac2->type = 8;
            $ac2->client_id = auth()->user()->headquarter->client_id;
        }

        $ac2->account = $request->cta_rel_purchases;
        $ac2->save();

        return response()->json(true);
    }

    public function indexReceipsFees()
    {
        return view('accountancy.receipt-fees.index');
    }

    public function generateReceipsFees(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $accountRH = AccountingAccount::where('type', 5)
                                        ->where('client_id', auth()->user()->headquarter->client_id)
                                        ->first();
        $accountRhRetention = AccountingAccount::where('type', 6)
                                                ->where('client_id', auth()->user()->headquarter->client_id)
                                                ->first();

        $shoppings = Shopping::with('provider', 'typeVoucher', 'detail.product', 'detail.centerCost')
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->where('status', '!=', 9)
                                ->where('type', 2)
                                ->whereBetween('date', [$from, $to])
                                ->get();

        $data = array();
        $cont = 0;
        $contDetail = 0;

        $movementInteger = (int) $request->movement;
        $movement = str_pad($movementInteger, 10, '0', STR_PAD_LEFT);
        $voucherInteger = (int) $request->voucher;
        $voucher = str_pad($voucherInteger, 6, '0', STR_PAD_LEFT);

        foreach ($shoppings as $shopping) {
            $data['head'][$cont]['movement'] = $movement;
            $data['head'][$cont]['anio'] = date('Y', strtotime($shopping->date));
            $data['head'][$cont]['periodo'] = date('m', strtotime($shopping->date));
            $data['head'][$cont]['tipo_libro'] = '03';
            $data['head'][$cont]['voucher'] = "03".date('m', strtotime($shopping->date)).$voucher;
            $data['head'][$cont]['fecha'] = date('d/m/Y', strtotime($shopping->date));
            $data['head'][$cont]['glosa'] = Str::upper($shopping->detail[0]->product->description);
            $data['head'][$cont]['tipo_moneda'] = '038';
            $data['head'][$cont]['credito_fiscal'] = '';
            $data['head'][$cont]['material_construccion'] = '';

            $item = 1;

            $data['body'][$contDetail]['movimiento'] = $movement;
            $data['body'][$contDetail]['anio'] = date('Y', strtotime($shopping->date));
            $data['body'][$contDetail]['periodo'] = date('m', strtotime($shopping->date));
            $data['body'][$contDetail]['tipo_libro'] = '03';
            $data['body'][$contDetail]['voucher'] = "03".date('m', strtotime($shopping->date)).$voucher;
            $data['body'][$contDetail]['cuenta'] = $accountRH->account;
            $data['body'][$contDetail]['item'] = $item;
            $data['body'][$contDetail]['glosa'] = Str::upper($shopping->detail[0]->product->description);
            $data['body'][$contDetail]['debe'] = '0.00';
            $data['body'][$contDetail]['haber'] = $shopping->subtotal;
            $data['body'][$contDetail]['tc'] = "0";
            $data['body'][$contDetail]['debe_ext'] = "0";
            $data['body'][$contDetail]['haber_ext'] = "0";
            $data['body'][$contDetail]['cos_ccodigo'] = $shopping->detail[0]->centerCost->code;
            $data['body'][$contDetail]['tipo_entidad'] = 'P';
            $data['body'][$contDetail]['cod_entidad'] = $shopping->provider->code;
            $data['body'][$contDetail]['as_tipo_doc'] = "02";
            $data['body'][$contDetail]['fecha'] = date('d/m/Y', strtotime($shopping->date));
            $data['body'][$contDetail]['serie_doc'] = $shopping->shopping_serie;
            $data['body'][$contDetail]['num_doc'] = str_pad($shopping->shopping_correlative, "8", '0', STR_PAD_LEFT);
            $data['body'][$contDetail]['fec_ven'] = "";
            $data['body'][$contDetail]['prov_canc'] = "P";
            $data['body'][$contDetail]['opera_tc'] = "SCV";
            $data['body'][$contDetail]['tipo_moneda'] = "038";

            $contDetail++;

            if ($shopping->has_retention) {
                $item = $item + 1;

                $data['body'][$contDetail]['movimiento'] = $movement;
                $data['body'][$contDetail]['anio'] = date('Y', strtotime($shopping->date));
                $data['body'][$contDetail]['periodo'] = date('m', strtotime($shopping->date));
                $data['body'][$contDetail]['tipo_libro'] = '03';
                $data['body'][$contDetail]['voucher'] = "03".date('m', strtotime($shopping->date)).$voucher;
                $data['body'][$contDetail]['cuenta'] = $accountRhRetention->account;
                $data['body'][$contDetail]['item'] = $item;
                $data['body'][$contDetail]['glosa'] = Str::upper($shopping->detail[0]->product->description);
                $data['body'][$contDetail]['debe'] = $shopping->total_retention;
                $data['body'][$contDetail]['haber'] = "0.00";
                $data['body'][$contDetail]['tc'] = "0";
                $data['body'][$contDetail]['debe_ext'] = "0";
                $data['body'][$contDetail]['haber_ext'] = "0";
                $data['body'][$contDetail]['cos_ccodigo'] = $shopping->detail[0]->centerCost->code;
                $data['body'][$contDetail]['tipo_entidad'] = 'P';
                $data['body'][$contDetail]['cod_entidad'] = $shopping->provider->code;
                $data['body'][$contDetail]['as_tipo_doc'] = "02";
                $data['body'][$contDetail]['fecha'] = date('d/m/Y', strtotime($shopping->date));
                $data['body'][$contDetail]['serie_doc'] = $shopping->shopping_serie;
                $data['body'][$contDetail]['num_doc'] = str_pad($shopping->shopping_correlative, "8", '0', STR_PAD_LEFT);
                $data['body'][$contDetail]['fec_ven'] = "";
                $data['body'][$contDetail]['prov_canc'] = "";
                $data['body'][$contDetail]['opera_tc'] = "SCV";
                $data['body'][$contDetail]['tipo_moneda'] = "038";

                $contDetail++;
            }

            $item = $item + 1;

            $data['body'][$contDetail]['movimiento'] = $movement;
            $data['body'][$contDetail]['anio'] = date('Y', strtotime($shopping->date));
            $data['body'][$contDetail]['periodo'] = date('m', strtotime($shopping->date));
            $data['body'][$contDetail]['tipo_libro'] = '03';
            $data['body'][$contDetail]['voucher'] = "03".date('m', strtotime($shopping->date)).$voucher;
            $data['body'][$contDetail]['cuenta'] = $shopping->detail[0]->product->account_expense;
            $data['body'][$contDetail]['item'] = $item;
            $data['body'][$contDetail]['glosa'] = Str::upper($shopping->detail[0]->product->description);
            $data['body'][$contDetail]['debe'] = $shopping->total;
            $data['body'][$contDetail]['haber'] = "0.00";
            $data['body'][$contDetail]['tc'] = "0";
            $data['body'][$contDetail]['debe_ext'] = "0";
            $data['body'][$contDetail]['haber_ext'] = "0";
            $data['body'][$contDetail]['cos_ccodigo'] = $shopping->detail[0]->centerCost->code;
            $data['body'][$contDetail]['tipo_entidad'] = 'P';
            $data['body'][$contDetail]['cod_entidad'] = $shopping->provider->code;
            $data['body'][$contDetail]['as_tipo_doc'] = "02";
            $data['body'][$contDetail]['fecha'] = date('d/m/Y', strtotime($shopping->date));
            $data['body'][$contDetail]['serie_doc'] = $shopping->shopping_serie;
            $data['body'][$contDetail]['num_doc'] = str_pad($shopping->shopping_correlative, 8, "0", STR_PAD_LEFT);
            $data['body'][$contDetail]['fec_ven'] = "";
            $data['body'][$contDetail]['prov_canc'] = "P";
            $data['body'][$contDetail]['opera_tc'] = "SCV";
            $data['body'][$contDetail]['tipo_moneda'] = "038";

            $contDetail++;

            $movementInteger = (int) $movement + 1;
            $movement = str_pad($movementInteger, 10, '0', STR_PAD_LEFT);
            $voucherInteger = (int) $voucher + 1;
            $voucher = str_pad($voucherInteger, 6, '0', STR_PAD_LEFT);

            $cont++;
        }

        return Excel::download(new ReceiptsFeesBookExport($data), 'DIARIO.xlsx');
    }
}
