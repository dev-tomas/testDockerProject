<?php

namespace App\Http\Controllers;

use PDF;
use App\Cash;
use App\Coin;
use App\Sale;
use App\Store;
use App\Client;
use App\Kardex;
use App\Product;
use App\Customer;
use App\Inventory;
use App\Warehouse;
use Carbon\Carbon;
use Dompdf\Dompdf;
use App\SaleDetail;
use App\BankAccount;
use App\Correlative;
use NumerosEnLetras;
use App\CreditClient;
use App\CashMovements;
use App\PaymentMethod;
use App\ProductPriceLog;
use App\SalePaymentMethod;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Storage;

class PosController extends Controller
{
    public $_ajax;
    public $_sunat;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->_ajax = new AjaxController();
        $this->_sunat = new SunatController();

        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function index()
    {
        $typeCash = auth()->user()->headquarter->client->cash_type;
        $cashes = Cash::where('client_id', auth()->user()->headquarter->client_id)
            ->where(function($query) use ($typeCash) {
                if ($typeCash == 0) {
                    $query->where('headquarter_id', $this->headquarter);
                } else {
                    $query->where('user_id', auth()->user()->id);
                }
            })
            ->where('status', 1)
            ->get(['id','name']);

        if ($cashes->isEmpty()) {
            toastr()->warning('No tiene una caja abierta para poder realizar ventas.');

            return redirect()->back();
        }

        $customers = $this->_ajax->getCustomers();
        $products = $this->_ajax->getProducts();
        $igv = $this->_ajax->getIgv();
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $currentDate = date('d-m-Y');
        $coins = Coin::all();
        $paymentMethods = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)->get(['name', 'id']);
        $bankAccounts = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '!=','3')->get();

        return view('pos.index', compact('customers', 'products', 'igv', 'clientInfo', 'currentDate', 'coins', 'cashes', 'paymentMethods', 'bankAccounts'));
    }

    public function storeSale(Request $request)
    {
        DB::beginTransaction();
        try {
            $igv_percentage = Auth::user()->headquarter->client->igv_percentage;
            $line_error = array();
            $ce = 0;
            $ae = 0;

            $correlatives = Correlative::where([
                ['headquarter_id', $this->headquarter],
                ['typevoucher_id', $request['typevoucher_id']],
                ['contingency', '0']
            ])->first();

            $setCorrelative = (int) $correlatives->correlative + 1;
            $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
            $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

            $correlative = Correlative::findOrFail($correlatives->id);
            $correlative->correlative = $final;
            $correlative->save();

            if($correlatives == null) {
                return response()->json(-2);
            }

            $paidout = 1;
            $statusCondition = 1; // 1: Pagado 0: Pendiente

            if ($request->condition == 'CREDITO 7 DIAS' || $request->condition == 'CREDITO 15 DIAS' || $request->condition == 'CREDITO 30 DIAS') {
                $paidout = 0;
                $statusCondition = 0;
            }

            $status = 1;
            
            $carbon = new \Carbon\Carbon();
            $date = $carbon->now();
            $sale = new Sale;
            $sale->date = $date->format('Y-m-d');
            $sale->serialnumber = $correlative->serialnumber;
            $sale->correlative = $final;
            $sale->change_type = $request->change_type;
            $sale->exonerated = $request->exonerated;
            $sale->unaffected = $request->c_unaffected;
            $sale->taxed = $request->c_taxed;
            $sale->igv = $request->c_igv;
            $sale->recharge = $request->recharge;
            $sale->igv_percentage = $igv_percentage;
            $sale->free = 0.00;
            $sale->othercharge = 0;
            $sale->discount = 0;
            $sale->subtotal = $request->c_taxed;
            $sale->icbper = $request->c_t;
            $sale->total_paying = $request->total_paying;
            $sale->balance = $request->balance;
            $sale->total = $request['c_total'];
            $sale->status = $status;
            $sale->issue = $date->format('Y-m-d');
            $sale->expiration = $date->addDays(7)->format('Y-m-d');
            $sale->coin_id = $request->coin;
            $sale->user_id = Auth::user()->id;
            $sale->typevoucher_id = $request->post('typevoucher_id');
            $sale->customer_id = $request->post('customer');
            $sale->headquarter_id = $this->headquarter;
            $sale->client_id = Auth::user()->headquarter->client_id;
            $sale->order = $request['order'];
            $sale->condition_payment = $request['condition'];
            $sale->condition_payment_amount = $request['mountPayment'];
            if ($request->has('otherCondition')) {
                $sale->other_condition = $request->otherCondition;
                $sale->other_condition_mount = $request->mountOtherPayment;
            }
            $sale->status_condition = $statusCondition;
            $sale->paidout = $paidout;
            $sale->detraction = $request->post('detraction');
            $sale->productregion = $request->post('product_region');
            $sale->serviceregion = $request->post('service_region');
            $sale->typeoperation_id = 1;
            $sale->detraction = $request['detraction'];
            $sale->observation = $request['t_detraction'] . ' | ' . $request['observation'];

            $kc = $request->serialnumber;;
            $ks = $final;

            if ($request->has('cash')) {
                $sale->cash_id = $request->cash;
            }

            if ($request->has('bank')) {
                $sale->bank_account_id = $request->bank;
            }

            if ($request->has('mp')) {
                $sale->payment_method_id = $request->mp;
            }
            if ($request->has('omp')) {
                $sale->other_payment_method_id = $request->omp;
            }
            if ($request->has('ocash')) {
                $sale->other_cash_id = $request->ocash;
            }

            if ($request->has('obank')) {
                $sale->other_bank_account_id = $request->obank;
            }

            if($sale->save()) {
                for ($x = 0; $x < count($request['cd_product']); $x++) {
                    $isService = Product::where('id', $request['cd_product'][$x])->first();

                        // dd($isService);
                        $verify = true;

                        if ($isService->operation_type == 2 || $isService->operation_type == 23) {
                            $verify = true;
                        } else {
                            $verify = $this->verifyStock($request['cd_quantity'][$x], $request['cd_product'][$x]);
                        }
                    if($verify) {
                        $price_unit = $request['cd_price'][$x] / (($igv_percentage / 100) + 1);
                        $saledetail = new SaleDetail;
                        $saledetail->price = $request['cd_price'][$x];
                        $saledetail->quantity = $request['cd_quantity'][$x];
                        $saledetail->igv = $request['cd_total'][$x] - $request['cd_subtotal'][$x];
                        $saledetail->subtotal = $request['cd_subtotal'][$x];
                        $saledetail->total = $request['cd_total'][$x];
                        $saledetail->product_id = $request['cd_product'][$x];
                        $saledetail->price_unit = $price_unit;
                        $saledetail->igv_percentage = $igv_percentage;

                        /**
                         * Operación Gravada
                         */
                        $saledetail->type_igv_id = 1;
                        $saledetail->sale_id = $sale->id;
                        $saledetail->save();
                        if ($status == 1) {
                            if ($isService->operation_type != 2 || $isService->operation_type != 23) {
                                $discountStock = $this->discountStock($request['cd_quantity'][$x], $request['cd_product'][$x],$sale->serialnumber, $sale->correlative, $request->change_type, $request->coin);
                            } else {
                                $discountStock = true;
                            }
                            
                            if($discountStock == false) {
                                return response()->json(-10);
                            }
                        }

                        /*$product_price_log = ProductPriceLog::where([
                            ['state', 1],
                            ['product_id', $request['cd_product'][$x]]
                        ])->first();

                        $product_price_log->stock   =   $product_price_log->stock - $request['cd_quantity'][$x];
                        $product_price_log->save();*/
                    } else {
                        DB::rollBack();
                        return response()->json(-9);
                    }
                    $ce++;
                }
            }

            $now = Carbon::now();

            if ($request->condition == 'CREDITO 7 DIAS' || $request->condition == 'CREDITO 15 DIAS' || $request->condition == 'CREDITO 30 DIAS') {
                if ($request->has('otherCondition')) {
                    $credito = new CreditClient;
                    $credito->date = $now->format('Y-m-d');
                    $credito->total = (float) $request['c_total'] - (float) $request->mountOtherPayment;
                    $credito->status = 0;       //0: PENDIENTE - 1: CANCELADO
                    $credito->send_email = 0;       //0: PENDIENTE - 1: CANCELADO
                    $credito->expiration = date('Y-m-d', strtotime(date('Y-m-d', strtotime($request->expiration))));
                    $credito->debt = (float) $request['c_total'] - (float) $request->mountOtherPayment;
                    $credito->sale_id = $sale->id;
                    $credito->client_id = Auth::user()->headquarter->client_id;
                    $credito->customer_id = $request['customer'];
                    $credito->save();
                } else {
                    $credito = new CreditClient;
                    $credito->date = $now->format('Y-m-d');
                    $credito->total = $request['c_total'];
                    $credito->status = 0;       //0: PENDIENTE - 1: CANCELADO
                    $credito->send_email = 0;       //0: PENDIENTE - 1: CANCELADO
                    $credito->expiration = date('Y-m-d', strtotime(date('Y-m-d', strtotime($request->expiration))));
                    $credito->debt = $request['c_total'];
                    $credito->sale_id = $sale->id;
                    $credito->client_id = Auth::user()->headquarter->client_id;
                    $credito->customer_id = $request['customer'];
                    $credito->save();
                }
            }

            if ($request->condition == 'EFECTIVO') {
                $movement = new CashMovements;
                $movement->movement = 'VENTA';
                $movement->amount = "{$sale->total}";
                $movement->observation = "{$sale->serialnumber}-{$sale->correlative}";
                $movement->cash_id = $request->cash;
                $movement->user_id = auth()->user()->id;
                $movement->save();
            }

            // for ($i=0; $i < count($request->amount); $i++) { 
            //     $payment = new SalePaymentMethod;
            //     $payment->amount = $request->amount[$i];
            //     $payment->payment_type = $request->method[$i];
            //     $payment->observation = $request->payment_note[$i];
            //     $payment->sale_id = $sale->id;
            //     $payment->save();
            // }

            DB::commit();

            $client = Auth::user()->headquarter->client;

            AppServiceProvider::constructInvoice($sale);

            $response['response'] = true;
            $response['pdf']    = Storage::disk('local')->url('pdf/' . Auth::user()->headquarter->client->document . '/' . $sale->serialnumber . '-' . $sale->correlative . '.pdf');
            $response['errors'] = $line_error;
            $response['sale_id'] = $sale->id;
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return $e->getMessage();
        }
    }

    public function discountStock($quantity, $product, $saleSerie, $saleCorrelative, $tc = null, $coin = 1)
    {
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;

        $stock = Store::where('product_id',$product)->where('warehouse_id',$mainWarehouseId)->first();
        $oldStock = $stock->stock;

        $newStock = (int) $oldStock - (int) $quantity;
        $stock->stock = $newStock;
        if ($stock->update()) {
            $oldInventary = Inventory::where('product_id', $product)
                ->where('warehouse_id', $mainWarehouseId)
                ->where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', $this->headquarter)->first();
            $oldInventaryStock = $oldInventary->amount_entered;
            $oldInventary->amount_entered = (int) $oldInventaryStock - (int) $quantity;
            $oldInventary->update();

            $kardex = new Kardex;
            $kardex->number = $saleSerie . '-' . $saleCorrelative;
            $kardex->type_transaction = 'Venta';
            $kardex->cost = $stock->product->cost;
            $kardex->output = (int) $quantity * -1;
            $kardex->balance = (int) $oldStock - (int) $quantity;
            $kardex->warehouse_id = $mainWarehouseId;
            $kardex->client_id = auth()->user()->headquarter->client_id;
            $kardex->product_id = $product;
            $kardex->save();
            return true;
        } else {
            return false;
        }
    }

    public function verifyStock($quantity, $product)
    {
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;

        $stock = DB::table('stores')->where([
            ['product_id', '=', $product],
            ['warehouse_id', '=', $mainWarehouseId]
        ])->first();

        if ($stock->stock >= $quantity) {
            return true;
        } else {
            return false;
        }
    }

    public function showPdfSale($id)
    {
        $util = \Util::getInstance();
        $sale = Sale::where('id', $id)->with('coin', 'type_voucher', 'customer')->first();
        $invoice = $this->_sunat->convertSale($id);
        $hash = $util->getHash($invoice);
        $qrCode = $this->getImage($invoice, $hash);
        $sale_detail = SaleDetail::where('sale_id', $id)->with('product', 'product.coin')->get();
        $decimal = Str::after($sale->total, '.');
        $int = Str::before($sale->total, '.');
        $leyenda = NumerosEnLetras::convertir($int) . ' con ' . $decimal . '/100';
        $bankInfo = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->first();
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $customerInfo = Customer::find($sale->customer_id);
        $igv = DB::table('taxes')->where('id', '=',1)->first();

        $data = array(
            'sale'         =>  $sale,
            'sale_detail'  =>  $sale_detail,
            'leyenda'           =>  $leyenda,
        );
        $html = view('commercial.sale.ticket',
                compact('sale','sale_detail','leyenda', 'bankInfo', 'clientInfo','customerInfo', 'invoice','igv', 'qrCode', 'hash'));
           
        $dompdf = new Dompdf();
        $GLOBALS['bodyHeight'] = 0;
        $dompdf->setCallbacks(
            array(
                'myCallbacks' => array(
                    'event' => 'end_frame', 'f' => function ($infos) {
                        $frame = $infos["frame"];
                        if (strtolower($frame->get_node()->nodeName) === "body") {
                            $padding_box = $frame->get_padding_box();
                            $GLOBALS['bodyHeight'] += (double) $padding_box['h'];
                        }
                    }
                )
            )
        );
        $dompdf->loadHtml($html);
        $dompdf->render();
        unset($dompdf);
        $dompdf = new Dompdf();
        $dompdf->set_paper('A4');
        $dompdf->loadHtml($html);
        $pdf = PDF::loadView('commercial.sale.ticket', compact('sale','sale_detail','leyenda', 'bankInfo', 'clientInfo','customerInfo', 'invoice','igv', 'qrCode', 'hash'));
        
        return $pdf->stream('VENTA ' . $sale->serialnumber . '-' . $sale->correlative . '.pdf');
    }

    public function getImage($sale, $hash)
    {
        $client = $sale->getClient();
        $params = [
            $sale->getCompany()->getRuc(),
            $sale->getTipoDoc(),
            $sale->getSerie(),
            $sale->getCorrelativo(),
            number_format($sale->getMtoIGV(), 2, '.', ''),
            number_format($sale->getMtoImpVenta(), 2, '.', ''),
            $sale->getFechaEmision()->format('Y-m-d'),
            $client->getTipoDoc(),
            $client->getNumDoc(),
        ];
        $content = implode('|', $params).'|';

        return $content . $hash;
    }
}
