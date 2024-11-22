<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Sunat\InvoiceController;
use PDF;
use Session;
use App\Cash;
use App\Coin;
use App\Sale;
use App\Taxe;
use DataTime;
use Response;
use App\Brand;
use App\Store;
use App\Client;
use App\Kardex;
use App\IgvType;
use App\Product;
use App\Summary;
use App\Category;
use App\Customer;
use App\DebitNote;
use App\Inventory;
use App\PriceList;
use App\Quotation;
use App\Warehouse;
use Carbon\Carbon;
use Dompdf\Dompdf;
use App\CreditNote;
use App\SaleDetail;
use App\BankAccount;
use App\Correlative;
use App\CostsCenter;
use App\HeadQuarter;
use App\SalePayment;
use App\TypeVoucher;
use NumerosEnLetras;
use App\CreditClient;
use App\TypeDocument;
use App\CashMovements;
use App\Mail\SendSale;
use App\OperationType;
use App\PaymentCredit;
use App\PaymentMethod;
use App\SummaryDetail;
use App\TypeDebitNote;
use App\TypeOperation;
use MongoDB\BSON\Type;
use App\Classification;
use App\TypeCreditNote;
use BaconQrCode\Writer;
use App\DebitNoteDetail;
use App\ProductPriceLog;
use App\QuotationDetail;
use Carbon\CarbonPeriod;
use App\CreditNoteDetail;
use App\LowCommunication;
use App\QuotationPayment;
use App\SaleReferralGuide;
use App\Mail\SendNoteDebit;
use App\Mail\SendQuotation;
use Illuminate\Support\Str;
use App\Exports\SalesExport;
use App\Mail\SendNoteCredit;
use Illuminate\Http\Request;
use App\LowCommunicationDetail;
use App\Exports\CustomersExport;
use App\Imports\CustomersImport;
use Illuminate\Support\Facades\DB;
use BaconQrCode\Renderer\Image\Png;
use Illuminate\Support\Facades\App;
use Greenter\Report\Render\QrRender;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Storage;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Validator;
use BaconQrCode\Common\ErrorCorrectionLevel;
use JeroenNoten\LaravelAdminLte\Menu\Builder;
use App\Http\Controllers\MethodsHelpController;

class CommercialController extends SunatController
{
    public $_ajax;
    public $_help;
    public $headquarter;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->middleware('can:cotizaciones.show')->only(['quotations', 'dt_quotation']);
        $this->middleware('can:cotizaciones.create')->only(['createQuotation', 'quotation']);
        $this->middleware('can:cotizaciones.edit')->only(['quotationEdit', 'editQuotation']);
        $this->middleware('can:cotizaciones.delete')->only('deleteQuotation');
        $this->middleware('can:cotizaciones.convert')->only('convertQuotation');
        $this->middleware('can:cotizaciones.send')->only('sendQuotation');

        $this->middleware('can:ventas.show')->only(['sales', 'dt_sales']);

        $this->middleware('can:clientes.show')->only(['customers', 'dt_customers']);
        $this->middleware('can:clientes.create')->only(['createCustomer']);
        $this->middleware('can:clientes.edit')->only(['updateCustomer']);
        $this->middleware('can:clientes.delete')->only(['deleteCustomer']);
        $this->middleware('can:clientes.import')->only(['importCustomers']);
        $this->middleware('can:clientes.export')->only(['exportCustomers']);

        $this->middleware('can:correlativos.show')->only(['configCorrelatives', 'dt_correlatives']);
        $this->middleware('can:correlativos.edit')->only(['getCorrelative']);
        // $this->middleware('can:correlativos.create')->only(['createCorrelative']);

        $this->_ajax = new AjaxController();
        $this->_help = new MethodsHelpController();
        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function index()
    {
    }

    public function quotations()
    {
        return view('commercial.quotation.index');
    }

    public function configCorrelatives()
    {
        $data = array(
            'typevouchers' => $this->_ajax->getTypeVouchers()
        );
        return view('commercial.correlative.config')->with($data);
    }

    public function quotation()
    {
        $date = date('d-m-Y');
        $correlative = DB::table('correlatives')->where([
            ['client_id', '=', Auth()->user()->headquarter->client_id],
            ['typevoucher_id', '=', 11]
        ])->first('correlative');
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $bankInfo = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '3')->first();

        $data = array(
            'customers' => $this->_ajax->getCustomers(),
            'typedocuments' => TypeDocument::all(),
            'coins' => Coin::all(),
            'products' => $this->_ajax->getProducts(),
            'igv' => $this->_ajax->getIgv(),
            'correlative' => $correlative,
            'currentDate' => $date,
            'currentDateLast' => date('d-m-Y', strtotime('+7 day', strtotime($date))),
            'categories' => Category::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'operations_type' => OperationType::all(),
            'brands' => Brand::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'clientInfo' => $clientInfo,
            'bankInfo' => $bankInfo,
            'classifications' => Classification::all(),
            'taxes' => Taxe::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'costsCenters' => CostsCenter::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'warehouses' => Warehouse::where([
                ['headquarter_id', Auth::user()->headquarter->id]
            ])->get(),
            'price_lists' => PriceList::where('client_id', auth()->user()->headquarter->client_id)->get(),
        );
        return view('commercial.quotation.create')->with($data);
    }

    public function quotationEdit($idQuotation)
    {
        $date = date('d-m-Y');
        $correlative = DB::table('correlatives')->where([
            ['client_id', '=', Auth()->user()->headquarter->client_id],
            ['typevoucher_id', '=', 11]
        ])->first('correlative');

        $quotation = Quotation::with('customer')->find($idQuotation);
        $quotationDetails = QuotationDetail::with('product', 'product.product_price_list.price_list')->where('quotation_id', $idQuotation)->get();

        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $bankInfo = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '3')->first();

        $data = array(
            'customers' => $this->_ajax->getCustomers(),
            'typedocuments' => TypeDocument::all(),
            'coins' => Coin::all(),
            'products' => $this->_ajax->getProducts(),
            'igv' => $this->_ajax->getIgv(),
            'correlative' => $correlative,
            'currentDate' => $date,
            'currentDateLast' => date('d-m-Y', strtotime('+7 day', strtotime($date))),
            'categories' => Category::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'operations_type' => OperationType::all(),
            'brands' => Brand::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'clientInfo' => $clientInfo,
            'bankInfo' => $bankInfo,
            'classifications' => Classification::all(),
            'taxes' => Taxe::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'costsCenters' => CostsCenter::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'warehouses' => Warehouse::where([
                ['headquarter_id', Auth::user()->headquarter->id]
            ])->get(),
            'price_lists' => PriceList::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'quotation' => $quotation,
            'details' => $quotationDetails
        );

        return view('commercial.quotation.edit')->with($data);
    }

    public function sale(int $type)
    {
        $typeCash = auth()->user()->headquarter->client->cash_type;
        $cashes = Cash::where('client_id', auth()->user()->headquarter->client_id)
            ->where(function ($query) use ($typeCash) {
                if ($typeCash == 0) {
                    $query->where('headquarter_id', $this->headquarter);
                } else {
                    $query->where('user_id', auth()->user()->id);
                }
            })
            ->where('status', 1)
            ->get(['id', 'name']);

        if ($cashes->isEmpty()) {
            toastr()->warning('No tiene una caja abierta para poder realizar ventas.');

            return redirect()->back();
        }

        $typeoperations = TypeOperation::get(['id', 'operation']);
        $date = date('d-m-Y');
        $bankAccounts = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '!=', '3')->get();
        $bankInfo = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '3')->first();
        $correlative = DB::table('correlatives')->where([
            ['headquarter_id', '=', $this->headquarter],
            ['typevoucher_id', '=', $type],
            ['contingency', '0']
        ])->get();
        $paymentMethods = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)->get(['name', 'id']);

        $clientInfo = Client::find(Auth::user()->headquarter->client_id);

        $returners = CreditNote::where('client_id', auth()->user()->headquarter->client_id)
                                ->where('headquarter_id', $this->headquarter)
                                ->where('condition_payment', 'DEVOLUCION')
                                ->where('is_returned', 0)
                                ->where('response_sunat', 1)
                                ->get(['id', 'serial_number', 'correlative', 'total']);

        $data = array(
            'customers' => $this->_ajax->getCustomers($type),
            'typedocuments' => $this->_ajax->getTypeDocuments(),
            'coins' => $this->_ajax->getCoins(),
            'products' => $this->_ajax->getProducts(),
            'igv' => $this->_ajax->getIgv(),
            'correlative' => $correlative,
            'currentDate' => $date,
            'currentDateLast' => $date,
            'type' => $type,
            'categories' => Category::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'operations_type' => OperationType::all(),
            'brands' => Brand::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'clientInfo' => $clientInfo,
            'bankInfo' => $bankInfo,
            'typeoperations' => $typeoperations,
            'igvType' => IgvType::all(),
            'bankAccounts' => $bankAccounts,
            'cashes' => $cashes,
            'paymentMethods' => $paymentMethods,
            'returners' => $returners,
        );

        return view('commercial.sale.create')->with($data);
    }

    public function getCreditNoteReturned(Request $request)
    {
        $returners = CreditNote::where('client_id', auth()->user()->headquarter->client_id)
                                ->where('headquarter_id', $this->headquarter)
                                ->where('condition_payment', 'DEVOLUCION')
                                ->where('is_returned', 0)
                                ->where('response_sunat', 1)
                                ->where('customer_id', $request->customer)
                                ->get(['id', 'serial_number', 'correlative', 'total']);

        return response()->json($returners);
    }

    public function getTypeIGV()
    {
        return IgvType::all()->toJson();
    }

    public function getCorrelativeS($serialnumber, $type)
    {
        $correlative = Correlative::where('serialnumber', $serialnumber)->where('typevoucher_id', $type)->where('headquarter_id', $this->headquarter)->where('contingency', '0')->first();

        return json_encode($correlative);
    }

    public function sales(Request $request)
    {
        if ($request->get('idQuotation') >= 0) {
            $idQuotation = $request->get('idQuotation');
        } else {
            $idQuotation = null;
        }

        $date = Carbon::now();

        $now = $date->format('Y-m-d');
        $lastMonth = $date->subDays(30);
        $lastYear = $date->subYears(1);
        $cashes = Cash::where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', auth()->user()->headquarter_id)->get(['id', 'name']);

        $ahora = Carbon::now();
        $desde = $ahora->copy()->firstOfMonth()->format('Y-m-d');
        $hasta = $ahora->copy()->lastOfMonth()->format('Y-m-d');

        $paymentMethods = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)->get(['name', 'id']);

        $paidLastMonth = $this->getPaidLastMonth($desde, $hasta);

        $defeatedSales = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->whereNull('low_communication_id')
            ->whereNull('credit_note_id')
            ->where('status_condition', 0)
            ->where('paidout', '!=', 1)
            ->whereDate('expiration', '<', date('Y-m-d'))
            ->where(function ($query) {
                if (
                    auth()->user()->hasRole('admin') ||
                    auth()->user()->hasRole('superadmin') ||
                    auth()->user()->hasRole('manager') ||
                    auth()->user()->hasPermissionTo('ventas.all')
                ) {
                } else {
                    $query->where('user_id', Auth::id());
                }
            })
            ->get(['id']);

        $defeated = CreditClient::query()->where('client_id', auth()->user()->headquarter->client_id)
            ->whereHas('sale', function ($query) use ($desde) {
                if (
                    auth()->user()->hasRole('admin') ||
                    auth()->user()->hasRole('superadmin') ||
                    auth()->user()->hasRole('manager') ||
                    auth()->user()->hasPermissionTo('ventas.all')
                ) {
                } else {
                    $query->where('user_id', Auth::id());
                }

                $query->whereNull('low_communication_id')
                    ->whereNull('credit_note_id')
                    ->where('status_condition', 0)
                    ->where('paidout', '!=', 1)
                    ->whereDate('expiration', '<', date('Y-m-d'));
            })
            ->where('status', 0);


        $pending = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->where(function ($query) {
                if (
                    auth()->user()->hasRole('admin') ||
                    auth()->user()->hasRole('superadmin') ||
                    auth()->user()->hasRole('manager') ||
                    auth()->user()->hasPermissionTo('ventas.all')
                ) {
                } else {
                    $query->where('user_id', Auth::id());
                }
            })
            ->whereNull('low_communication_id')
            ->whereNull('credit_note_id')
            ->where('status_condition', 0)
            ->where('paidout', '!=', 1)
//                    ->where('issue', '>=', $desde)
            ->where('expiration', '>=', date('Y-m-d'))
            ->get()
            ->pluck(['id']);

        $pend = CreditClient::whereIn('sale_id', $pending)->sum('debt');

        $data = array(
            'idQuotation' => $idQuotation,
            'paidLastMonth' => $paidLastMonth,
            'defeated' => $defeated,
            'defeatedSales' => $defeatedSales,
            'pending' => $pending,
            'pend' => $pend,
            'bankAccounts' => BankAccount::where('client_id', Auth::user()->headquarter->client_id)->get(),
            'cashes' => $cashes,
            'paymentMethods' => $paymentMethods
        );

        return view('commercial.sale.index')->with($data);
    }

    public function getPaidLastMonth($desde, $hasta)
    {
        $paidLastMonth = 0;
        $creditNoteTotal = 0;
        $count = 0;
        $sales = Sale::with('credit_note', 'debit_note')->whereBetween('issue', [$desde, $hasta])
            ->whereNull('low_communication_id')
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->where('status', 1)
            ->where('condition_payment', '!=', 'CREDITO')
            ->where(function ($query) {
                if (
                    auth()->user()->hasRole('admin') ||
                    auth()->user()->hasRole('superadmin') ||
                    auth()->user()->hasRole('manager') ||
                    auth()->user()->hasPermissionTo('ventas.all')
                ) {
                } else {
                    $query->where('user_id', Auth::id());
                }

                $query->where('paidout', 1);
                // $query->where('client_id', auth()->user()->headquarter->client_id);
            })->get();
        foreach ($sales as $sale) {
            $paidLastMonth += $sale->total;

            if (isset($sale->credit_note)) {
                $paidLastMonth -= $sale->credit_note->total;
            }

            if (isset($sale->debit_note)) {
                $paidLastMonth += $sale->debit_note->total;
            }

            $count++;
        }


        $paymentQuery = PaymentCredit::where('client_id', auth()->user()->headquarter->client_id)
            ->whereBetween('date', [$desde, $hasta]);

        return array(
            'total' => $paidLastMonth + $paymentQuery->sum('payment'),
            'count' => $count + $paymentQuery->count()
        );
    }

    public function downloadXML($file)
    {
        $name = $file . '.xml';
        $route = '/xml/' . Auth::user()->headquarter->client->document . '/' . $name;

        $headers = array(
            'Content-Type: application/xml',
        );

        return Storage::disk('public')->download($route, $name, $headers);
    }

    public function downloadCdr($file)
    {
        $name = $file;// . '.zip';
        $route = '/cdr/' . Auth::user()->headquarter->client->document . '/' . $name;

        $headers = array(
            'Content-Type: application/zip',
        );

        return Storage::disk('public')->download($route, $name, $headers);
    }

    public function customers()
    {
        $data = array(
            'typedocuments' => $this->_ajax->getTypeDocuments(),
        );
        return view('commercial.customer.index')->with($data);
    }

    /**
     * Crud Quotation
     */

    public function getLastCustomerCode()
    {
        $lastCode = Customer::where('client_id', auth()->user()->headquarter->client_id)->orderBy('id', 'desc')->select('code')->first();

        if ($lastCode != null) {
            if (is_numeric($lastCode->code)) {
                $code = (int)$lastCode->code + 1;
            } else {
                $code = 1;
            }
        } else {
            $code = 1;
        }

        return response()->json(str_pad($code, 5, 0, STR_PAD_LEFT));
    }

    public function createCustomer(Request $request)
    {
        try {
            /**
             * Validate Form send with ajax
             */
            $validator = Validator::make($request->all(), [
                'typedocument' => 'required',
                'document' => 'required|min:2',
                'description' => 'required'
            ]);

            if ($validator->fails()) {
                return -2;
            }

            $existCustomer = Customer::where('document', $request->document)->where('client_id', auth()->user()->headquarter->client_id)->first();

            $existCustomerWithCode = Customer::where('code', $request->code)->where('client_id', auth()->user()->headquarter->client_id)->first();

            if ($existCustomer != null || $existCustomerWithCode != null) {
                return response()->json(-99);
            }

            $customer = new Customer;
            $customer->description = $request['description'];
            $customer->code = $request->code;
            $customer->document = $request['document'];
            $customer->phone = $request['phone'];
            $customer->address = $request['address'];
            $customer->typedocument_id = $request['typedocument'];
            $customer->client_id = Auth::user()->headquarter->client_id;
            $customer->email = $request['email'];
            $customer->secondary_email = $request['emailOptional'];
            $customer->detraction = $request['detraction'];
            $customer->contact = $request['contact'];
            $customer->user_id = auth()->user()->id;

            if ($customer->save()) {
                echo json_encode(true);
            }
        } catch (\Exception $e) {
            echo $e->getCode();
            $rpta = '';
            switch ($e->getCode()) {
                case '23000':
                    $rpta = 'Éste cliente ya está registrado.';
                    break;
                default:
//                    $rpta = 'No se pudo';/*??*/
                    $rpta = 'Ocurrio un error inesperado';
                    break;
            }

            echo $rpta;
        }
    }

    public function updateCustomer(Request $request)
    {
        try {
            /**
             * Validate Form send with ajax
             */
            $validator = Validator::make($request->all(), [
                'typedocument' => 'required',
                // 'document' => 'required|min:8',
                'description' => 'required'
            ]);

            if ($validator->fails()) {
                return -2;
            }

            $customer = Customer::find($request['customer_id']);
            $customer->description = $request['description'];
            $customer->document = $request['document'];
            $customer->phone = $request['phone'];
            $customer->address = $request['address'];
            $customer->typedocument_id = $request['typedocument'];
            $customer->email = $request['email'];
            $customer->code = $request->code;
            $customer->detraction = $request->detraction;
            $customer->contact = $request->contact;
            $customer->secondary_email = $request->emailOptional;

            if ($customer->save()) {
                echo json_encode(true);
            }
        } catch (\Exception $e) {
            echo $e->getCode();
            $rpta = '';
            switch ($e->getCode()) {
                case '23000':
                    $rpta = 'Éste cliente ya está registrado.';
                    break;
            }

            return $rpta;
        }
    }

    public function deleteCustomer(Request $request)
    {
        $quotation = Quotation::where('customer_id', $request->get('customer_id'))->count();
        $customer = Customer::find($request->get('customer_id'));

        if ($quotation > 0) {
            return response()->json(false);
        } else {
            return response()->json($customer->delete());
        }
    }

    public function createQuotation(Request $request)
    {
        DB::beginTransaction();
        try {
            $correlatives = Correlative::where([
                ['client_id', '=', Auth()->user()->headquarter->client_id],
                ['typevoucher_id', 13],
            ])->first();

            $setCorrelative = (int)$correlatives->correlative + 1;
            $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
            $final = str_repeat('0', ($repeat >= 0) ? $repeat : 0) . $setCorrelative;

            $correlative = Correlative::findOrFail($correlatives->id);
            $correlative->correlative = $final;
            $correlative->save();

            $detraction = 0;
            if ($request['detraction'] != null) {
                $detraction = $request['detraction'];
            }
            $product_region = 0;
            if ($request['product_region'] != null) {
                $product_region = $request['product_region'];
            }

            $service_region = 0;
            if ($request['service_region'] != null) {
                $service_region = $request['service_region'];
            }

            $quotation = new Quotation;
            $quotation->date = date('Y-m-d', strtotime($request['date']));
            $quotation->correlative = $final;
            $quotation->serial_number = $correlatives->serialnumber;
            $quotation->exonerated = $request['c_exonerated'];
            $quotation->unaffected = $request['c_unaffected'];
            $quotation->taxed = $request['c_taxed'];
            $quotation->igv = $request['c_igv'];
            $quotation->free = $request['c_free'];
            $quotation->icbper = $request->has("c_t") ? $request->c_t : 0.00;
            $quotation->othercharge = 0;
            $quotation->recharge = $request->recharge;
            $quotation->discount = 0;
            $quotation->subtotal = $request['c_taxed'];
            $quotation->total = $request['c_total'];
            $quotation->status = 1;
            $quotation->issue = date('Y-m-d');
            $quotation->expiration = date('Y-m-d', strtotime($request['expiration']));
            $quotation->coin_id = $request['coin'];
            $quotation->user_id = auth()->user()->id;
            $quotation->typevoucher_id = 11;
            $quotation->customer_id = $request['customer'];
            $quotation->headquarter_id = $this->headquarter;
            $quotation->order = $request['order'];
            $quotation->condition = $request['condition'];
            $quotation->change_type = $request['change_type'];
            $quotation->detraction = $detraction;
            $quotation->product_region = $product_region;
            $quotation->service_region = $service_region;
            $quotation->stateProduction = $request['estateQuotation'];
            $quotation->observation = $request['t_detraction'] . ' | ' . $request['observation'];
            $quotation->credit_frecuency = $request->dueFrecuency;
            $quotation->credit_time = $request->numberDues;
            $quotation->is_order_note = $request->filled('ordernote');
            if ($quotation->save()) {
                for ($x = 0; $x < count($request['cd_price']); $x++) {
                    $quotationdetail = new QuotationDetail;
                    $quotationdetail->price = $request['cd_price'][$x];
                    $quotationdetail->unity = $request['cd_quantity'][$x];
                    $quotationdetail->igv = $request['cd_total'][$x] - $request['cd_subtotal'][$x];
                    $quotationdetail->subtotal = $request['cd_subtotal'][$x];
                    $quotationdetail->total = $request['cd_total'][$x];
                    $quotationdetail->product_id = $request['cd_product'][$x];
                    $quotationdetail->quotation_id = $quotation->id;
                    $quotationdetail->detail = $request['cd_detail'][$x];
                    $quotationdetail->save();
                }
            }

            if ($request->condition == 'CREDITO') {
                for ($i = 0; $i < count($request->payment_amount); $i++) {
                    $p = new QuotationPayment;
                    $p->date = date('Y-m-d', strtotime($request['payment_date'][$i]));
                    $p->mount = $request['payment_amount'][$i];
                    $p->quotation_id = $quotation->id;
                    $p->save();
                }
            }

            DB::commit();

            $rpta = array(
                'response' => true,
                'quotation_id' => $quotation->id
            );
            echo json_encode($rpta);
        } catch (\Exception $e) {
            DB::rollBack();
            echo json_encode($e->getMessage());
        }
    }

    public function editQuotation(Request $request)
    {
        DB::beginTransaction();
        try {

            $detraction = 0;
            if ($request['detraction'] != null) {
                $detraction = $request['detraction'];
            }
            $product_region = 0;
            if ($request['product_region'] != null) {
                $product_region = $request['product_region'];
            }

            $service_region = 0;
            if ($request['service_region'] != null) {
                $service_region = $request['service_region'];
            }

            $quotation = Quotation::find($request['idQuotation']);
            $quotation->date = date('Y-m-d', strtotime($request['date']));
            $quotation->exonerated = $request['c_exonerated'];
            $quotation->unaffected = $request['c_unaffected'];
            $quotation->taxed = $request['c_taxed'];
            $quotation->igv = $request['c_igv'];
            $quotation->free = $request['c_free'];
            $quotation->icbper = $request->has("c_t") ? $request->c_t : 0.00;
            $quotation->othercharge = 0;
            $quotation->recharge = $request->recharge;
            $quotation->discount = 0;
            $quotation->subtotal = $request['c_taxed'];
            $quotation->total = $request['c_total'];
            $quotation->status = 1;
            $quotation->issue = date('Y-m-d');
            $quotation->expiration = date('Y-m-d', strtotime($request['expiration']));
            $quotation->coin_id = $request['coin'];
            $quotation->user_id = auth()->user()->id;
            $quotation->typevoucher_id = 11;
            $quotation->customer_id = $request['customer'];
            $quotation->headquarter_id = $this->headquarter;
            $quotation->order = $request['order'];
            $quotation->condition = $request['condition'];
            $quotation->change_type = $request['change_type'];
            $quotation->detraction = $detraction;
            $quotation->product_region = $product_region;
            $quotation->service_region = $service_region;
            $quotation->stateProduction = $request['estateQuotation'];
            $quotation->observation = $request['t_detraction'] . ' | ' . $request['observation'];
            $quotation->credit_frecuency = $request->dueFrecuency;
            $quotation->credit_time = $request->numberDues;
            if ($quotation->update()) {
                $quotationdetail = QuotationDetail::where('quotation_id', $request['idQuotation'])->get()->each(function ($item, $key) {
                    $item->delete();
                });

                for ($x = 0; $x < count($request['cd_price']); $x++) {
                    $quotationdetail = new QuotationDetail;
                    $quotationdetail->price = $request['cd_price'][$x];
                    $quotationdetail->unity = $request['cd_quantity'][$x];
                    $quotationdetail->igv = $request['cd_total'][$x] - $request['cd_subtotal'][$x];
                    $quotationdetail->subtotal = $request['cd_subtotal'][$x];
                    $quotationdetail->total = $request['cd_total'][$x];
                    $quotationdetail->product_id = $request['cd_product'][$x];
                    $quotationdetail->quotation_id = $quotation->id;
                    $quotationdetail->detail = $request['cd_detail'][$x];
                    $quotationdetail->save();
                }
            }

            if ($request->condition == 'CREDITO') {
                $oldPayments = QuotationPayment::where('quotation_id', $quotation->id)->get()->each(function ($item, $key) {
                    $item->delete();
                });

                for ($i = 0; $i < count($request->payment_amount); $i++) {
                    $p = new QuotationPayment;
                    $p->date = date('Y-m-d', strtotime($request['payment_date'][$i]));
                    $p->mount = $request['payment_amount'][$i];
                    $p->quotation_id = $quotation->id;
                    $p->save();
                }
            }

            DB::commit();

            $rpta = array(
                'response' => true,
                'quotation_id' => $request['idQuotation']
            );
            echo json_encode($rpta);
        } catch (\Exception $e) {
            DB::rollBack();
            echo json_encode($e->getMessage());
        }
    }

    public function deleteQuotation(Request $request)
    {
        $quotation = Quotation::find($request->get('quotation_id'));
        $quotation->status = 0;
        return response()->json($quotation->save());
    }

    public function createSale(Request $request)
    {
        DB::beginTransaction();
        try {
            $igv_percentage = Auth::user()->headquarter->client->igv_percentage;
            $quotation = 0;
            $quotationdetail = 0;
            $line_error = array();
            $ce = 0;
            $ae = 0;
            $now = Carbon::now();

            if ($request->has('quotation_id')) {
                $quotation = Quotation::where('id', $request['quotation_id'])->first();
                $quotationdetail = QuotationDetail::where('quotation_id', $request['quotation_id'])->get();
            } else {
                $quotation = null;
                $quotationdetail = null;
            }

            if ($request->has('serialnumber')) {
                $correlatives = Correlative::where([
                    ['serialnumber', $request->serialnumber],
                    ['headquarter_id', $this->headquarter],
                    ['typevoucher_id', $request['typevoucher_id']],
                    ['contingency', '0']
                ])->first();
            } else {
                $correlatives = Correlative::where([
                    ['headquarter_id', $this->headquarter],
                    ['typevoucher_id', $request['typevoucher_id']],
                    ['contingency', '0']
                ])->first();
            }


            $setCorrelative = (int)$correlatives->correlative + 1;
            $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
            $final = str_repeat('0', ($repeat >= 0) ? $repeat : 0) . $setCorrelative;

            $correlative = Correlative::findOrFail($correlatives->id);
            $correlative->correlative = $final;
            $correlative->save();

            if ($correlatives == null) {
                return response()->json(-2);
            }

            $paidout = 1;
            $statusCondition = 1; // 1: Pagado 0: Pendiente

            if ($request->condition == 'CREDITO') {
                $paidout = 0;
                $statusCondition = 0;
            }

            $status = 1;
            if ($request->typegenerate == 'draft') {
                $status = 2;
            } else if ($request->typegenerate == 'commit') {
                $status = 1;
            }
            $carbon = new \Carbon\Carbon();
            $date = $carbon->now();

            if ($quotation == null) {
                $sale = new Sale;
                $sale->date = $date->format('Y-m-d');
                $sale->serialnumber = $request->serialnumber;
                $sale->correlative = $final;
                $sale->change_type = $request->change_type;
                $sale->exonerated = $request->c_exonerated;
                $sale->unaffected = $request->c_unaffected;
                $sale->taxed = $request->c_taxed;
                $sale->igv = $request->c_igv;
                $sale->free = $request->c_free;
                $sale->othercharge = 0;
                $sale->discount = $request->c_discount;
                $sale->recharge = $request->recharge;
                $sale->subtotal = $request->c_taxed;
                $sale->icbper = $request->c_t;
                $sale->total = $request['c_total'];
                $sale->status = $status;
                $sale->issue = $date->format('Y-m-d');
                $sale->expiration = date('Y-m-d', strtotime($request->expiration));
                $sale->coin_id = $request->coin;
                $sale->igv_percentage = $igv_percentage;
                $sale->user_id = Auth::user()->id;
                $sale->typevoucher_id = $request->post('typevoucher_id');
                $sale->customer_id = $request['customer'];
                $sale->headquarter_id = $this->headquarter;
                $sale->client_id = Auth::user()->headquarter->client_id;
                $sale->order = $request['order'];
                $sale->paidout = $paidout;
                $sale->condition_payment = $request['condition'];
                $sale->credit_time = $request->dueFrecuency;
                $sale->condition_payment_amount = $request['mountPayment'];
                if ($request->condition != 'CREDITO') {
                    $sale->can_change_payment = 0;
                }
                if ($request->condition == 'DEVOLUCION') {
                    $sale->return_id = $request->credit_note_return;
                }
                if ($request->has('otherCondition')) {
                    $sale->other_condition = $request->otherCondition;
                    $sale->other_condition_mount = $request->mountOtherPayment;
                }
                $sale->status_condition = $statusCondition;
                $sale->detraction = $request->post('detraction');
                $sale->paidout = $paidout;
                $sale->productregion = $request->post('product_region');
                $sale->serviceregion = $request->post('service_region');
                $sale->typeoperation_id = $request->typeoperation;
                $sale->detraction = $request['detraction'];
                $sale->observation = $request['t_detraction'] . ' | ' . $request['observation'];

                if ($request['condition'] == 'EFECTIVO' && $request->has('cash')) {
                    $sale->cash_id = $request->cash;
                }

                if ($request['condition'] == 'DEPOSITO EN CUENTA' && $request->has('bank')) {
                    $sale->bank_account_id = $request->bank;
                }

                if (($request['condition'] == 'TARJETA DE CREDITO' && $request['condition'] == 'TARJETA DE DEBITO') && $request->has('mp')) {
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

                $kc = $request->serialnumber;;
                $ks = $final;
            } else {// convertir cotizacion a venta
                $sale = new Sale;
                $sale->date = date('Y-m-d');
                $sale->serialnumber = $correlatives->serialnumber;
                $sale->correlative = $final;
                $sale->change_type = $quotation->change_type;//$request->change_type;
                $sale->exonerated = $quotation->exonerated;
                $sale->unaffected = $quotation->unaffected;
                $sale->taxed = $quotation->taxed;
                $sale->igv = $quotation->igv;
                $sale->igv_percentage = $igv_percentage;
                $sale->free = $quotation->free;
                $sale->othercharge = $quotation->othercharge;
                $sale->recharge = $quotation->recharge;
                $sale->discount = $quotation->discount;
                $sale->subtotal = $quotation->subtotal;
                $sale->total = $quotation->total;
                $sale->status = $quotation->status;
                $sale->issue = $quotation->issue;
                $sale->expiration = $quotation->expiration;
                $sale->coin_id = $quotation->coin_id;
                $sale->user_id = $quotation->user_id;
                $sale->typevoucher_id = $request['typevoucher_id'];
                $sale->customer_id = $quotation->customer_id;
                $sale->headquarter_id = $quotation->headquarter_id;
                $sale->client_id = Auth::user()->headquarter->client_id;
                $sale->condition_payment = $quotation->condition;
                $sale->condition_payment_amount = $quotation->total;
                $sale->status_condition = $statusCondition;
                $sale->quotation_id = $quotation->id;
                $sale->typeoperation_id = 1;
                $sale->detraction = $quotation->detraction;
                $sale->observation = $quotation->observation;
                $sale->paidout = $paidout;

                $kc = $correlatives->serialnumber;
                $ks = $final;

                $quotationUpdate = Quotation::find($quotation->id);
                $quotationUpdate->stateProduction = 1;
                $quotationUpdate->update();
            }

            if ($sale->save()) {
                if ($quotationdetail == null) {
                    if ($request->condition == 'CREDITO') {
                        for ($i = 0; $i < count($request->payment_amount); $i++) {
                            $p = new SalePayment;
                            $p->date = date('Y-m-d', strtotime($request['payment_date'][$i]));
                            $p->mount = $request['payment_amount'][$i];
                            $p->sale_id = $sale->id;
                            $p->save();
                        }
                    }

                    for ($x = 0; $x < count($request['cd_product']); $x++) {
                        $isService = Product::where('id', $request['cd_product'][$x])->first();

                        // dd($isService->operation_type != "23");
                        $verify = true;

                        if ($isService->operation_type == 2 || $isService->operation_type == 23) {
                            $verify = true;
                        } else {
                            $verify = $this->verifyStock($request['cd_quantity'][$x], $request['cd_product'][$x]);
                        }

                        if ($verify) {
                            $price_unit = $request['cd_price'][$x] / (($igv_percentage / 100) + 1);
                            $saledetail = new SaleDetail;
                            $saledetail->price = $request['cd_price'][$x];
                            $saledetail->quantity = $request['cd_quantity'][$x];
                            $saledetail->igv = $request['cd_total'][$x] - $request['cd_subtotal'][$x];
                            $saledetail->subtotal = $request['cd_subtotal'][$x];
                            $saledetail->total = $request['cd_total'][$x];
                            $saledetail->product_id = $request['cd_product'][$x];
                            $saledetail->type_igv_id = $request['is_free'][$x] == 1 ? 6 : null;
                            $saledetail->sale_id = $sale->id;
                            $saledetail->price_unit = $price_unit;
                            $saledetail->igv_percentage = $igv_percentage;
                            $saledetail->save();

                            if ($status == 1) {
                                if ($isService->operation_type != 2 || $isService->operation_type != 23) {
                                    $discountStock = $this->discountStock($request['cd_quantity'][$x], $request['cd_product'][$x], $sale->serialnumber, $sale->correlative, $request->change_type, $request->coin);
                                } else {
                                    $discountStock = true;
                                }

                                if ($discountStock == false) {
                                    return response()->json(-10);
                                }
                            }
                        } else {
                            return response()->json(-9);
                        }
                        $ce++;
                    }

                    if ($request->g_type[0] != null) {
                        for ($i = 0; $i < count($request->g_type); $i++) {
                            $guide = new SaleReferralGuide;
                            $guide->type = $request->g_type[$i];
                            $guide->serie = $request->g_serialnumber[$i];
                            $guide->sale_id = $sale->id;
                            $guide->save();
                        }
                    }
                } else {

                    $quotationUpdate = Quotation::find($quotation->id);
                    $quotationUpdate->sale_id = $sale->id;
                    $quotationUpdate->update();

                    foreach ($quotationdetail as $qd) {
                        $isService = Product::where('id', $qd->product_id)->first();
                        $verify = true;

                        if ($isService->operation_type == 2) {
                            $verify = true;
                        } else {
                            $verify = $this->verifyStock($qd->unity, $qd->product_id);
                        }

                        if ($verify != false) {
                            $price_unit = $qd->price / (($igv_percentage / 100) + 1);
                            $saledetail = new SaleDetail;
                            $saledetail->price = $qd->price;
                            $saledetail->quantity = $qd->unity;
                            $saledetail->igv = $qd->igv;
                            $saledetail->subtotal = $qd->subtotal;
                            $saledetail->total = $qd->total;
                            $saledetail->product_id = $qd->product_id;
                            $saledetail->type_igv_id = $quotation->free > 0.00 ? 6 : 1;
                            $saledetail->sale_id = $sale->id;
                            $saledetail->price_unit = $price_unit;
                            $saledetail->igv_percentage = $igv_percentage;
                            $saledetail->save();

                            if ($isService->operation_type != 2) {
                                if ($quotation != null) {
                                    $discountStock = $this->discountStock($qd->unity, $qd->product_id, $sale->serialnumber, $sale->correlative);
                                }
                            } else {
                                $discountStock = true;
                            }

                            $line_error[$ce] = 0;
                        } else {
                            $line_error[$ce] = 1;
                            $ae++;
                        }
                        $ce++;
                    }

                    if ($quotation->condition == 'CREDITO') {
                        foreach ($quotation->payments as $payment) {
                            $p = new SalePayment;
                            $p->date = $payment->date;
                            $p->mount = $payment->mount;
                            $p->sale_id = $sale->id;
                            $p->save();
                        }
                    }
                }
            }

            if ($request->condition == 'CREDITO') {
                if ($request->has('otherCondition')) {
                    $credito = new CreditClient;
                    $credito->date = $now->format('Y-m-d');
                    $credito->total = (float)$request['c_total'] - (float)$request->mountOtherPayment;
                    $credito->status = 0;       //0: PENDIENTE - 1: CANCELADO
                    $credito->send_email = 0;       //0: PENDIENTE - 1: CANCELADO
                    $credito->expiration = date('Y-m-d', strtotime(date('Y-m-d', strtotime($request->expiration))));
                    $credito->debt = (float)$request['c_total'] - (float)$request->mountOtherPayment;
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

            if ($request->condition == 'DEVOLUCION') {
                $creditNoteReturn = CreditNote::find($request->credit_note_return);
                $creditNoteReturn->is_returned = 1;
                $creditNoteReturn->save();
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

            DB::commit();

            $client = Auth::user()->headquarter->client;

            (new InvoiceController)->constructInvoice($sale);

//            AppServiceProvider::constructInvoice($sale);

            $response['response'] = true;
            $response['errors'] = $line_error;
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();

            return $e->getMessage();
        }
    }

    public function enviarSoloSunat($id)
    {
        $sale = Sale::find($id);

        $state = AppServiceProvider::constructInvoice($sale);

        return response()->json($state);
    }

    public function saleEdit($type, $id)
    {
        $typeoperations = TypeOperation::get(['id', 'operation']);
        $date = date('d-m-Y');
        $bankInfo = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '3')->first();
        $correlative = DB::table('correlatives')->where([
            ['headquarter_id', '=', $this->headquarter],
            ['typevoucher_id', '=', $type],
            ['contingency', '0']
        ])->get();

        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $data = array(
            'customers' => $this->_ajax->getCustomers(),
            'typedocuments' => $this->_ajax->getTypeDocuments(),
            'coins' => $this->_ajax->getCoins(),
            'products' => $this->_ajax->getProducts(),
            'igv' => $this->_ajax->getIgv(),
            'correlative' => $correlative,
            'currentDate' => $date,
            'currentDateLast' => date('d-m-Y', strtotime('+7 day', strtotime($date))),
            'type' => $type,
            'categories' => Category::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'operations_type' => OperationType::all(),
            'brands' => Brand::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'clientInfo' => $clientInfo,
            'bankInfo' => $bankInfo,
            'typeoperations' => $typeoperations,
            'sale' => Sale::find($id),
            'igvType' => IgvType::all()
        );

        return view('commercial.sale.edit')->with($data);
    }

    public function updateSale(Request $request)
    {
        DB::beginTransaction();
        try {
            $line_error = array();
            $ce = 0;
            $ae = 0;

            if ($request->typegenerate == 'draft') {
                $status = 2;
            } else if ($request->typegenerate == 'commit') {
                $status = 1;
            }

            $carbon = new \Carbon\Carbon();
            $date = $carbon->now();

            $sale = Sale::find($request->sid);
            $sale->date = $date->format('Y-m-d');
            $sale->exonerated = $request->c_exonerated;
            $sale->unaffected = $request->c_unaffected;
            $sale->taxed = $request->c_taxed;
            $sale->igv = $request->c_igv;
            $sale->free = $request->c_free;
            $sale->othercharge = 0;
            $sale->discount = 0;
            $sale->subtotal = $request->c_taxed;
            $sale->icbper = $request->c_t;
            $sale->total = $request['c_total'];
            $sale->status = $status;
            $sale->issue = $date->format('Y-m-d');
            $sale->expiration = $date->addDays(7)->format('Y-m-d');
            $sale->coin_id = $request->coin;
            $sale->user_id = Auth::user()->id;
            $sale->typevoucher_id = $request->post('typevoucher_id');
            $sale->customer_id = $request['customer'];
            $sale->headquarter_id = $this->headquarter;
            $sale->client_id = Auth::user()->headquarter->client_id;
            $sale->order = $request['order'];
            $sale->condition_payment = $request['condition'];
            $sale->detraction = $request->post('detraction');
            $sale->paidout = 1;
            $sale->productregion = $request->post('product_region');
            $sale->serviceregion = $request->post('service_region');
            $sale->typeoperation_id = $request->typeoperation;
            $sale->observation = $request['t_detraction'] . ' | ' . $request['observation'];

            $kc = $sale->correlative;
            $ks = $sale->serialnumber;

            if ($sale->save()) {
                for ($i = 0; $i < count($request->sdid); $i++) {
                    $dsd = SaleDetail::find($request->sdid[$i]);
                    $dsd->delete();
                }
                for ($x = 0; $x < count($request['cd_product']); $x++) {
                    $verify = $this->verifyStock($request['cd_quantity'][$x], $request['cd_product'][$x]);
                    if ($verify) {
                        $saledetail = new SaleDetail;
                        $saledetail->price = $request['cd_price'][$x];
                        $saledetail->quantity = $request['cd_quantity'][$x];
                        $saledetail->igv = $request['cd_total'][$x] - $request['cd_subtotal'][$x];
                        $saledetail->subtotal = $request['cd_subtotal'][$x];
                        $saledetail->total = $request['cd_total'][$x];
                        $saledetail->product_id = $request['cd_product'][$x];
                        $saledetail->type_igv_id = $request['type_igv'][$x];
                        $saledetail->sale_id = $sale->id;
                        $saledetail->save();

                        if ($status == 1) {
                            $discountStock = $this->discountStock($request['cd_quantity'][$x], $request['cd_product'][$x], $sale->serialnumber, $sale->correlative);
                            if ($discountStock == false) {
                                return response()->json(-10);
                            }
                        }
                        $line_error[$ce] = 0;
                    }
                    $ce++;
                }

                if ($request->g_type[0] != null) {
                    for ($i = 0; $i < count($request->g_type); $i++) {
                        $guide = new SaleReferralGuide;
                        $guide->type = $request->g_type[$i];
                        $guide->serie = $request->g_serialnumber[$i];
                        $guide->sale_id = $sale->id;
                        $guide->save();
                    }
                }
            }

            DB::commit();

            if ($status == 1) {
                if ($this->sendSunat($sale->id)) {
                    $line_error[$ce] = 9;
                }
            }
            $response['response'] = true;
            $response['errors'] = $line_error;
            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function exportSales(Request $request)
    {
        $now = new \DateTime();
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->date, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->date, '- '))->format('Y-m-d');

        return Excel::download(new SalesExport($to, $from, $this->headquarter, $request->status), 'Comprobantes [' . $now->format('d-m-y') . '].xlsx');
    }

    public function verifyStock($quantity, $product)
    {
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;

        $store = Store::where([
            ['product_id', '=', $product],
            ['warehouse_id', '=', $mainWarehouseId]
        ])->select('stock')->first();

        $current_stock = $store->stock;

        if ($current_stock >= $quantity) {
            return true;
        } else {
            return false;
        }
    }

    public function discountStock($quantity, $product, $saleSerie, $saleCorrelative, $tc = null, $coin = 1)
    {
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;

        $producto = Product::find($product);

        $stock = Store::where('product_id', $product)->where('warehouse_id', $mainWarehouseId)->first();
        $oldStock = $stock->stock;

        $newStock = (int)$oldStock - (int)$quantity;
        $stock->stock = $newStock;
        if ($stock->update()) {
            $oldInventary = Inventory::where('product_id', $product)
                ->where('warehouse_id', $mainWarehouseId)
                ->where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', $this->headquarter)->first();
            if ($oldInventary != null) {
                $oldInventaryStock = $oldInventary->amount_entered;
                $oldInventary->amount_entered = (int)$oldInventaryStock - (int)$quantity;
                $oldInventary->update();
            }

            $kardex = new Kardex;
            $kardex->number = $saleSerie . '-' . $saleCorrelative;
            $kardex->type_transaction = 'Venta';
            $kardex->output = (int)$quantity * -1;
            $kardex->balance = (int)$oldStock - (int)$quantity;
            $kardex->cost = $producto->cost;
            $kardex->warehouse_id = $mainWarehouseId;
            $kardex->client_id = auth()->user()->headquarter->client_id;
            $kardex->product_id = $product;
            $kardex->coin_id = $coin;
            $kardex->exchange_rate = $tc;
            $kardex->save();
            return true;
        } else {
            return false;
        }
    }

    public function dt_correlatives(Request $request)
    {
        $typevoucher_id = $request->get('typevoucher_id');

        return datatables()->of(
            Db::table('correlatives')
                ->join('typevouchers', 'correlatives.typevoucher_id', '=', 'typevouchers.id')
                ->where('headquarter_id', '=', $this->headquarter)
                ->where(function ($query) use ($typevoucher_id) {
                    if ($typevoucher_id != null) {
                        $query->where('typevoucher_id', '=', $typevoucher_id);
                    }
                })
                ->get([
                    'typevouchers.description as tv_description',
                    'correlatives.serialnumber',
                    'correlatives.correlative',
                    'correlatives.id',
                ])
        )->toJson();
    }

    public function dt_customers(Request $request)
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')) {
            return datatables()->of(
                Db::table('customers')
                    ->join('typedocuments', 'customers.typedocument_id', '=', 'typedocuments.id')
                    ->where(function ($query) use ($request) {
                        if ($request->get('denomination') != '') {
                            $query->where('customers.description', 'like', '%' . $request->get('denomination') . '%')
                                ->orWhere('customers.document', 'like', '%' . $request->get('denomination') . '%');
                        }
                    })
                    ->where('customers.client_id', auth()->user()->headquarter->client_id)
                    ->get([
                        'typedocuments.description as td_description',
                        'customers.description as c_description',
                        'customers.document',
                        'customers.phone',
                        'customers.address',
                        'customers.email',
                        'customers.id',
                        'customers.code'
                    ])
            )->toJson();
        } else {
            return datatables()->of(
                Db::table('customers')
                    ->join('typedocuments', 'customers.typedocument_id', '=', 'typedocuments.id')
                    ->where(function ($query) use ($request) {
                        if ($request->get('denomination') != '') {
                            $query->where('customers.description', 'like', '%' . $request->get('denomination') . '%');
                        }
                    })
                    ->where('customers.client_id', auth()->user()->headquarter->client_id)
                    ->get([
                        'typedocuments.description as td_description',
                        'customers.description as c_description',
                        'customers.document',
                        'customers.phone',
                        'customers.address',
                        'customers.email',
                        'customers.id',
                        'customers.code'
                    ])
            )->toJson();
        }
    }

    public function dt_quotation(Request $request)
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')) {
            return datatables()->of(
                Db::table('quotations')
                    ->leftjoin('customers', 'quotations.customer_id', '=', 'customers.id')
                    ->leftjoin('typevouchers', 'quotations.typevoucher_id', '=', 'typevouchers.id')
                    ->leftjoin('coins', 'quotations.coin_id', '=', 'coins.id')
                    ->leftjoin('sales', 'sales.quotation_id', '=', 'quotations.id')
                    ->where('quotations.headquarter_id', $this->headquarter)
                    ->where(function ($query) use ($request) {
                        $query->where('quotations.status', 1);
                        if ($request->get('denomination') != '') {
                            $query->where('customers.description', 'like', '%' . $request->get('denomination') . '%');
                        }

                        if ($request->get('document') != '') {
                            $query->where('quotations.correlative', 'like', '%' . $request->get('document') . '%');
                        }

                        if ($request->get('dateOne') != '') {
                            $query->whereBetween('quotations.date', [$request->get('dateOne'), $request->get('dateTwo')]);
                        }
                    })
                    ->get([
                        'quotations.date',
                        'quotations.correlative',
                        'quotations.serial_number',
                        'customers.document',
                        'customers.description as c_description',
                        'coins.symbol',
                        'quotations.total',
                        'quotations.free',
                        'quotations.id',
                        'quotations.sendemail',
                        'customers.email as customer_email',
                        'customers.typedocument_id',
                        'quotations.stateProduction',
                        'quotations.sale_id',
                        'sales.serialnumber',
                        'sales.correlative as salesCorrelative',
                    ])
            )->toJson();
        } else {
            return datatables()->of(
                Db::table('quotations')
                    ->leftjoin('customers', 'quotations.customer_id', '=', 'customers.id')
                    ->leftjoin('typevouchers', 'quotations.typevoucher_id', '=', 'typevouchers.id')
                    ->leftjoin('coins', 'quotations.coin_id', '=', 'coins.id')
                    ->leftjoin('sales', 'sales.quotation_id', '=', 'quotations.id')
                    ->where('quotations.headquarter_id', $this->headquarter)
                    ->where('quotations.user_id', auth()->user()->id)
                    ->where(function ($query) use ($request) {
                        $query->where('quotations.status', 1);
                        if ($request->get('denomination') != '') {
                            $query->where('customers.description', 'like', '%' . $request->get('denomination') . '%');
                        }

                        if ($request->get('document') != '') {
                            $query->where('quotations.correlative', 'like', '%' . $request->get('document') . '%');
                        }

                        if ($request->get('dateOne') != '') {
                            $query->whereBetween('quotations.date', [$request->get('dateOne'), $request->get('dateTwo')]);
                        }
                    })
                    ->get([
                        'quotations.date',
                        'quotations.correlative',
                        'quotations.serial_number',
                        'customers.document',
                        'customers.description as c_description',
                        'coins.symbol',
                        'quotations.total',
                        'quotations.free',
                        'quotations.id',
                        'quotations.sendemail',
                        'customers.email as customer_email',
                        'customers.typedocument_id',
                        'quotations.stateProduction',
                        'quotations.sale_id',
                        'sales.serialnumber',
                        'sales.correlative as salesCorrelative',
                    ])
            )->toJson();
        }
    }

    public function getTotalsQuotations(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $totalQuoations = Quotation::where('headquarter_id', $this->headquarter)
            ->whereBetween('date', [$from, $to])
            ->where('status', 1)
            ->where(function ($query) use ($request) {
                if ($request->get('document') != '') {
                    $query->where('correlative', 'like', '%' . $request->get('document') . '%');
                }

                if (!auth()->user()->hasRole('admin') || !auth()->user()->hasRole('superadmin') ||
                    !auth()->user()->hasPermissionTo('ventas.all')) {
                    $query->where('user_id', auth()->user()->id);
                }
            })
            ->whereHas('customer', function ($query) use ($request) {
                if ($request->get('denomination') != '') {
                    $query->where('description', 'like', '%' . $request->get('denomination') . '%');
                }
            })
            ->get()->sum('total');


        $data = ['totalQuoations' => number_format($totalQuoations, 2, '.', '')];

        return response()->json($data);
    }

    public function dt_sales_2($type, Request $request)
    {
        $summaries = Summary::with('detail', 'detail.sale', 'sunat_code')
            ->whereBetween('date_generation', [$request->dateOne, $request->dateTwo])
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->get();
        return datatables()->of(
            $summaries
        )->editColumn('date_generation', function ($dt) {
            return date('d-m-Y', strtotime($dt->date_generation));
        })->editColumn('date_issues', function ($dt) {
            return date('d-m-Y', strtotime($dt->date_issues));
        })->toJson();
    }

    public function dt_sales(Request $request)
    {
        $date = Carbon::now();
        $now = $date->format('Y-m-d');
        $lastMonth = $date->subDays(30);
        $lastYear = $date->subYears(1);

        $ahora = Carbon::now();
        $desde = $ahora->firstOfMonth()->format('Y-m-d');
        $hasta = $ahora->lastOfMonth()->format('Y-m-d');

        if (auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasPermissionTo('ventas.all')) {
            $sales = Sale::with('customer',
                'coin',
                'type_voucher',
                'sunat_code',
                'operation',
                'credit_note', 'credit_note.sunat_code',
                'debit_note', 'debit_note.sunat_code',
                'low_communication',
                'headquarter',
                'credito')
                ->where([
                    ['sales.headquarter_id', $this->headquarter],
                    ['sales.client_id', auth()->user()->client_id]
                ])
                ->whereBetween('date', [$request->get('dateOne'), $request->get('dateTwo')])
                ->where(function ($query) use ($request, $lastMonth, $desde, $hasta) {
                    if ($request->get('denomination') != '') {
                        $query->whereHas('customer', function ($q) use ($request) {
                            $q->where('customers.description', 'like', '%' . $request->get('denomination') . '%');
                        });
                    }

                    if ($request->get('serial') != '') {
                        $query->where('correlative', 'like', '%' . $request->get('serial') . '%');
                    }

                    if ($request->get('idQuotation') != '') {
                        $query->where('quotation_id', $request->get('idQuotation'));
                    }

                    if ($request->status == 4) { // PAGADOS
                        $query->where('status_condition', 1)
                            ->where('paidout', 1);
                    }

                    if ($request->status == 2) {    // PENDIENTE
                        $query->where('expiration', '>=', date('Y-m-d'))
                            ->where('status_condition', 0)
                            ->whereNull('credit_note_id')
                            ->where('paidout', '!=', 1);
                    }

                    if ($request->status == 3) { // PENDIENTE VENCIDOS
                        $query->whereBetween('expiration', [$request->get('dateOne'), date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->whereNull('credit_note_id')
                            ->where('paidout', '!=', 1);

                    }
                })
                ->get();
        } else {
            $sales = Sale::with('customer',
                'coin',
                'type_voucher',
                'sunat_code',
                'operation',
                'credit_note', 'credit_note.sunat_code',
                'debit_note', 'debit_note.sunat_code',
                'low_communication',
                'headquarter',
                'credito')
                ->where('sales.headquarter_id', $this->headquarter)
                ->where('sales.client_id', auth()->user()->client_id)
                ->where('sales.user_id', auth()->user()->id)
                ->whereBetween('date', [$request->get('dateOne'), $request->get('dateTwo')])
                ->where(function ($query) use ($request) {
                    if ($request->get('denomination') != '') {
                        $query->whereHas('customer', function ($q) use ($request) {
                            $q->where('customers.description', 'like', '%' . $request->get('denomination') . '%');
                        });
                    }

                    if ($request->get('serial') != '') {
                        $query->where('serialnumber', 'like', '%' . $request->get('serial') . '%');
                    }

                    if ($request->get('dateOne') != '') {
                        $query->whereBetween('date', [$request->get('dateOne'), $request->get('dateTwo')]);
                    }

                    if ($request->get('idQuotation') != '') {
                        $query->where('quotation_id', $request->get('idQuotation'));
                    }

                    if ($request->status == 4) { // PAGADOS
                        $query->where('status_condition', 1)
                            ->where('paidout', 1);
                    }

                    if ($request->status == 2) {    // PENDIENTE
                        $query->where('expiration', '>=', date('Y-m-d'))
                            ->where('status_condition', 0)
                            ->where('paidout', '!=', 1);
                    }

                    if ($request->status == 3) { // PENDIENTE VENCIDOS
                        $query->whereBetween('expiration', [$request->get('dateOne'), date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->get();
        }


        return datatables()->of($sales)->toJson();
    }

    public function getTotalSales(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');
        

        $sale = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->where('headquarter_id', $this->headquarter)
            ->where('status', 1)
            ->whereNull('low_communication_id')
            ->whereNull('credit_note_id')
            ->where('coin_id', 1)
            ->whereBetween('date', [$from, $to])
            ->where(function ($query) use ($request,$from) {
                if ($request->get('document') != '') {
                    $query->where('sales.correlative', 'like', '%' . $request->get('document') . '%');
                }
                if ($request->status =='4') {
                    $query->where('status_condition', 1);
                }
                if ($request->status == '3') {
                    $query->where('status_condition', 0)->whereBetween('expiration', [$from, date('Y-m-d')]);
                }
                if($request->status =='2'){
                    $query->where('status_condition', 0)->where('expiration', '>=', date('Y-m-d'));
                }      
            })
            ->where(function ($query) use ($request, $from, $to) {
                if ($request->get('document') != '') {
                    $query->where('sales.correlative', 'like', '%' . $request->get('document') . '%');
                }
            })
            ->whereHas('customer', function ($query) use ($request) {
                if ($request->get('denomination') != '') {
                    $query->where('description', 'like', '%' . $request->get('denomination') . '%');
                }
            })
            ->get(['total']);

        $saleFacturasSoles = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->where('headquarter_id', $this->headquarter)
            ->where('status', 1)
            ->whereNull('low_communication_id')
            ->whereNull('credit_note_id')
            ->where('coin_id', 1)
            ->whereBetween('date', [$from, $to])
            ->where(function ($query) use ($request,$from) {
                if ($request->get('document') != '') {
                    $query->where('sales.correlative', 'like', '%' . $request->get('document') . '%');
                }
                if ($request->status =='4') {
                    $query->where('status_condition', 1);
                }
                if ($request->status == '3') {
                    $query->where('status_condition', 0)->whereBetween('expiration', [$from, date('Y-m-d')]);
                }
                if($request->status =='2'){
                    $query->where('status_condition', 0)->where('expiration', '>=', date('Y-m-d'));
                }      
            })
            ->whereHas('customer', function ($query) use ($request) {
                if ($request->get('denomination') != '') {
                    $query->where('description', 'like', '%' . $request->get('denomination') . '%');
                }
            })
            ->where('coin_id', 1)
            ->where('typevoucher_id', 1)
            ->get(['total']);

        $saleBoletasSoles = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->where('headquarter_id', $this->headquarter)
            ->where('status', 1)
            ->whereNull('low_communication_id')
            ->whereNull('credit_note_id')
            ->where('coin_id', 1)
            ->whereBetween('date', [$from, $to])
            ->where(function ($query) use ($request,$from) {
                if ($request->get('document') != '') {
                    $query->where('sales.correlative', 'like', '%' . $request->get('document') . '%');
                }
                if ($request->status =='4') {
                    $query->where('status_condition', 1);
                }
                if ($request->status == '3') {
                    $query->where('status_condition', 0)->whereBetween('expiration', [$from, date('Y-m-d')]);
                }
                if($request->status =='2'){
                    $query->where('status_condition', 0)->where('expiration', '>=', date('Y-m-d'));
                }      
            })
            ->whereHas('customer', function ($query) use ($request) {
                if ($request->get('denomination') != '') {
                    $query->where('description', 'like', '%' . $request->get('denomination') . '%');
                }
            })
            ->where('coin_id', 1)
            ->where('typevoucher_id', 2)
            ->get(['total']);

        $saleSoles = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->where('headquarter_id', $this->headquarter)
            ->where('status', 1)
            ->whereNull('low_communication_id')
            ->whereNull('credit_note_id')
            ->where('coin_id', 1)
            ->whereBetween('date', [$from, $to])
            ->where(function ($query) use ($request,$from) {
                if ($request->get('document') != '') {
                    $query->where('sales.correlative', 'like', '%' . $request->get('document') . '%');
                }
                if ($request->status =='4') {
                    $query->where('status_condition', 1);
                }
                if ($request->status == '3') {
                    $query->where('status_condition', 0)->whereBetween('expiration', [$from, date('Y-m-d')]);
                }
                if($request->status =='2'){
                    $query->where('status_condition', 0)->where('expiration', '>=', date('Y-m-d'));
                }      
            })
            ->whereHas('customer', function ($query) use ($request) {
                if ($request->get('denomination') != '') {
                    $query->where('description', 'like', '%' . $request->get('denomination') . '%');
                }
            })
            ->where('coin_id', 1)
            ->get(['total'])->sum('total');

        $totalPending = CreditClient::query()->whereHas('sale', function ($query) use ($from, $to, $request) {
            $query->where('coin_id', 1);
            $query->whereBetween('date', [$from, $to]);
            $query->where('client_id', auth()->user()->headquarter->client_id);
            if (
                auth()->user()->hasRole('admin') ||
                auth()->user()->hasRole('superadmin') ||
                auth()->user()->hasRole('manager') ||
                auth()->user()->hasPermissionTo('ventas.all')
            ) {
            } else {
                $query->where('user_id', Auth::id());
            }

            $query->whereNull('low_communication_id')
                ->whereNull('credit_note_id')
                ->where('status_condition', 0)
                ->where('paidout', '!=', 1);
                
            if ($request->status == '2') {
                $query->where('expiration', '>=', date('Y-m-d'));
            } else if ($request->status == '3') {
                $query->whereBetween('expiration', [$from, date('Y-m-d')]);
            }
        })
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->where('status', 0)
            ->get();

        $saleUsd = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->where('headquarter_id', $this->headquarter)
            ->where('status', 1)
            ->whereNull('low_communication_id')
            ->whereNull('credit_note_id')
            ->where('coin_id', 2)
            ->whereBetween('date', [$from, $to])
            ->where(function ($query) use ($request,$from) {
                if ($request->get('document') != '') {
                    $query->where('sales.correlative', 'like', '%' . $request->get('document') . '%');
                }
                if ($request->status =='4') {
                    $query->where('status_condition', 1);
                }
                if ($request->status == '3') {
                    $query->where('status_condition', 0)->whereBetween('expiration', [$from, date('Y-m-d')]);
                }
                if($request->status =='2'){
                    $query->where('status_condition', 0)->where('expiration', '>=', date('Y-m-d'));
                }      
            })
            ->whereHas('customer', function ($query) use ($request) {
                if ($request->get('denomination') != '') {
                    $query->where('description', 'like', '%' . $request->get('denomination') . '%');
                }
            })
            ->get(['total'])->count();

        $salesUsd = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->where('headquarter_id', $this->headquarter)
            ->where('status', 1)
            ->whereNull('low_communication_id')
            ->whereNull('credit_note_id')
            ->where('coin_id', 2)
            ->whereBetween('date', [$from, $to])
            ->where(function ($query) use ($request,$from) {
                if ($request->get('document') != '') {
                    $query->where('sales.correlative', 'like', '%' . $request->get('document') . '%');
                }
                if ($request->status =='4') {
                    $query->where('status_condition', 1);
                }
                if ($request->status == '3') {
                    $query->where('status_condition', 0)->whereBetween('expiration', [$from, date('Y-m-d')]);
                }
                if($request->status =='2'){
                    $query->where('status_condition', 0)->where('expiration', '>=', date('Y-m-d'));
                }      
            })
            ->whereHas('customer', function ($query) use ($request) {
                if ($request->get('denomination') != '') {
                    $query->where('description', 'like', '%' . $request->get('denomination') . '%');
                }
            })
            ->get(['total']);

        $saleFacturasUsd = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->where('headquarter_id', $this->headquarter)
            ->where('status', 1)
            ->whereNull('low_communication_id')
            ->whereNull('credit_note_id')
            ->where('coin_id', 2)
            ->whereBetween('date', [$from, $to])
            ->where(function ($query) use ($request,$from) {
                if ($request->get('document') != '') {
                    $query->where('sales.correlative', 'like', '%' . $request->get('document') . '%');
                }
                if ($request->status =='4') {
                    $query->where('status_condition', 1);
                }
                if ($request->status == '3') {
                    $query->where('status_condition', 0)->whereBetween('expiration', [$from, date('Y-m-d')]);
                }
                if($request->status =='2'){
                    $query->where('status_condition', 0)->where('expiration', '>=', date('Y-m-d'));
                }      
            })
            ->whereHas('customer', function ($query) use ($request) {
                if ($request->get('denomination') != '') {
                    $query->where('description', 'like', '%' . $request->get('denomination') . '%');
                }
            })
            ->where('typevoucher_id', 1)
            ->get(['total']);

        $saleBoletasUsd = Sale::where('client_id', auth()->user()->headquarter->client_id)
            ->where('headquarter_id', $this->headquarter)
            ->where('status', 1)
            ->whereNull('low_communication_id')
            ->whereNull('credit_note_id')
            ->where('coin_id', 2)
            ->whereBetween('date', [$from, $to])
            ->where(function ($query) use ($request,$from) {
                if ($request->get('document') != '') {
                    $query->where('sales.correlative', 'like', '%' . $request->get('document') . '%');
                }
                if ($request->status =='4') {
                    $query->where('status_condition', 1);
                }
                if ($request->status == '3') {
                    $query->where('status_condition', 0)->whereBetween('expiration', [$from, date('Y-m-d')]);
                }
                if($request->status =='2'){
                    $query->where('status_condition', 0)->where('expiration', '>=', date('Y-m-d'));
                }      
            })
            ->whereHas('customer', function ($query) use ($request) {
                if ($request->get('denomination') != '') {
                    $query->where('description', 'like', '%' . $request->get('denomination') . '%');
                }
            })
            ->where('typevoucher_id', 2)
            ->get(['total']);
        $totalPendingUsd = CreditClient::query()->whereHas('sale', function ($query) use ($from, $to, $request) {
            $query->where('coin_id', 2);
            $query->whereBetween('date', [$from, $to]);
            $query->where('client_id', auth()->user()->headquarter->client_id);
            if (
                auth()->user()->hasRole('admin') ||
                auth()->user()->hasRole('superadmin') ||
                auth()->user()->hasRole('manager') ||
                auth()->user()->hasPermissionTo('ventas.all')
            ) {
            } else {
                $query->where('user_id', Auth::id());
            }

            $query->whereNull('low_communication_id')
                ->whereNull('credit_note_id')
                ->where('status_condition', 0)
                ->where('paidout', '!=', 1);
            if ($request->status == '2') {
                $query->where('expiration', '>=', date('Y-m-d'));
            } else if ($request->status == '3') {
                $query->whereBetween('expiration', [$from, date('Y-m-d')]);
            }
        })
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->where('status', 0)
            ->get();

        $sales = [
            'salesBoletasSoles' => number_format($saleBoletasSoles->sum('total'), 2, '.', ' '),
            'salesFacturasSoles' => number_format($saleFacturasSoles->sum('total'), 2, '.', ' '),
            'sales_count' => $sale->count(),
            'salesSoles' => number_format($saleSoles, 2, '.', ' '),
            'totalPending' => number_format($totalPending->sum('debt'), 2, '.', ' '),
            'docPending' => number_format($totalPending->count(), 2, '.', ' '),

            'salesBoletasUsd' => number_format($saleBoletasUsd->sum('total'), 2, '.', ' '),
            'salesFacturasUsd' => number_format($saleFacturasUsd->sum('total'), 2, '.', ' '),
            'sales_count_usd' => $saleUsd,
            'salesUsd' => number_format($salesUsd->sum('total'), 2, '.', ' '),
            'totalPendingUsd' => $totalPendingUsd->sum('debt'),
            'docPendingUsd' => number_format($totalPendingUsd->count(), 2, '.', ' '),
        ];

        return response()->json($sales);
    }

    public function getCustomer(Request $request)
    {
        echo json_encode(
            DB::table('customers')->where('id', '=', $request['customer_id'])->first()
        );
    }

    public function getCustomers($type = null)
    {
        echo json_encode(
            $this->_ajax->getCustomers($type)
        );
    }

    /**
     * Correlatives
     */
    public function createCorrelative(Request $request)
    {
        if (isset($request['correlative_id'])) {
            $correlative = Correlative::find($request['correlative_id']);
        } else {
            $correlative = new Correlative;
        }

        $correlative = new Correlative;
        $correlative->serialnumber = $request['serialnumber'];
        $correlative->correlative = $request['correlative'];
        $correlative->headquarter_id = $this->headquarter;
        $correlative->typevoucher_id = $request['typevoucher'];

        if ($correlative->save()) {
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    }

    public function getCorrelative(Request $request)
    {
        echo json_encode($this->_ajax->getCorrelative($request->correlative_id));
    }

    /**
     * Customers
     */
    public function importCustomers(Request $request)
    {
        Excel::import(new CustomersImport, $request->file('file'));

        toastr()->success('Se importaron los clientes con éxito.');

        return back();
    }

    public function exportCustomers()
    {
        $now = new \DateTime();
        return Excel::download(new CustomersExport, 'Clientes [' . $now->format('d-m-y') . '].xlsx');

    }

    public function exportCustomersTemplate()
    {
        $file = public_path() . "/templates/Plantilla_Clientes.xlsx";

        $headers = array(
            'Content-Type: application/xlsx',
        );
        return Response::download($file, 'Plantilla Clientes.xlsx', $headers);
        // return Response::download($file, $headers);
    }

    /**
     * Quotations
     */
    public function showPdfQuotation($id)
    {
        $document = Quotation::with('customer', 'detalles', 'headquarter.client')->find($id);
        $quotation = $document;
        $quotation_detail = $document->detalles;
        $clientInfo = $document->headquarter->client;
        $decimal = Str::after($quotation->total, '.');
        $int = Str::before($quotation->total, '.');
        $leyenda = NumerosEnLetras::convertir($int) . ' con ' . $decimal . '/100';
        $bankInfo = BankAccount::where('client_id', $clientInfo->id)->get();
        $igv = DB::table('taxes')->where('id', '=', 1)->first();

        if($clientInfo->quotation_size == 'a4') {
            $html = view("commercial.quotation.pdf", compact('quotation', 'quotation_detail', 'leyenda', 'bankInfo', 'clientInfo', 'igv'));
        } else if($clientInfo->quotation_size == 'ticket') {
            $html = view('commercial.quotation.ticket', compact('quotation', 'quotation_detail', 'leyenda', 'bankInfo', 'clientInfo', 'igv'));
        }

        $dompdf = new Dompdf();
        $dompdf->set_option('isRemoteEnabled', true);
        if ($clientInfo->quotation_size == 'a4') {
            $dompdf->set_paper('A4');
        } else if ($clientInfo->quotation_size == 'ticket') {
            $dompdf->set_paper([0, 0, 200, 140]);
        }
        $dompdf->load_html($html);
        $dompdf->render();
        $page_count = $dompdf->get_canvas()->get_page_number();
        unset($dompdf);
        $dompdf = new DOMPDF();
        $dompdf->set_option('isRemoteEnabled', true);
        if ($clientInfo->quotation_size == 'a4') {
            $dompdf->set_paper('A4');
        } else if ($clientInfo->quotation_size == 'ticket') {
            $dompdf->set_paper([0, 0, 200, 140 * ($page_count) - ($page_count * 32)]);
        }
        $dompdf->load_html($html);
        $dompdf->render();
        return $dompdf->stream('COTIZACION ' . $quotation->serial_number . '-' . $quotation->correlative . '.pdf', ['Attachment' => 0]);
    }

    public function downloadPdfQuotation($id)
    {
        $quotation = Quotation::find($id);
        $quotation_detail = QuotationDetail::where('quotation_id', $id)->get();
        $data = array(
            'quotation' => $quotation,
            'quotation_detail' => $quotation_detail
        );
        $pdf = PDF::loadView('commercial.quotation.pdf', $data)->setPaper('a4');
        return $pdf->download('Cotización_' . date('d-m-Y') . '.pdf');
    }

    public function sendQuotation(Request $request)
    {
        try {
            $quotation = Quotation::find($request->post('quotation_id'));
            $quotation_detail = QuotationDetail::where('quotation_id', $request->post('quotation_id'))->get();
            $decimal = Str::after($quotation->total, '.');
            $int = Str::before($quotation->total, '.');
            $leyenda = NumerosEnLetras::convertir($int) . ' con ' . $decimal . '/100';
            $bankInfo = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->get();
            $clientInfo = Client::find(Auth::user()->headquarter->client_id);
            $igv = DB::table('taxes')->where('id', '=', 1)->first();
            $data = array(
                'quotation' => $quotation,
                'quotation_detail' => $quotation_detail,
                'clientInfo' => $clientInfo,
                'leyenda' => $leyenda,
                'bankInfo' => $bankInfo,
                'igv' => $igv
            );
            $pdf = PDF::loadView('commercial.quotation.pdf', $data)->setPaper('a4');

            Mail::to($request->post('email'))
                ->send(
                    new SendQuotation(
                        $request->post('quotation_id'),
                        $pdf,
                        $clientInfo
                    )
                );
            if (Mail::failures()) {
                return response()->json('No se pudo enviar el Correo');
            } else {
                $quotation = Quotation::find($request->post('quotation_id'));
                $quotation->sendemail = 1;
                $quotation->save();
                echo json_encode(true);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
            return response()->json(false);
        }
    }

    public function consultaCDR($sale, $type, $client = null)
    {
        if ($type == 1) {
            if ($client != null) {
                $invoice = Sale::with('type_voucher')->where('client_id', $client)->find($sale);
                $client = Client::find($client);
                $document = $client->document;
                $serialnumber = $invoice->serialnumber;
            } else {
                $invoice = Sale::with('type_voucher')->where('client_id', auth()->user()->headquarter->client_id)->find($sale);
                $client = auth()->user()->headquarter->client;
                $document = auth()->user()->headquarter->client->document;
                $serialnumber = $invoice->serialnumber;
            }
        } else {
            if ($client != null) {
                $invoice = CreditNote::with('type_voucher')->where('client_id', $client)->find($sale);
                $client = Client::find($client);
                $document = $client->document;
                $serialnumber = $invoice->serial_number;
            } else {
                $invoice = CreditNote::with('type_voucher')->where('client_id', auth()->user()->headquarter->client_id)->find($sale);
                $client = auth()->user()->headquarter->client;
                $document = auth()->user()->headquarter->client->document;
                $serialnumber = $invoice->serial_number;
            }
        }
        $arguments = [
            $document,
            $invoice->type_voucher->code,
            $serialnumber,
            $invoice->correlative
        ];

        $cdr = (new SunatController)->consultCDR($arguments, $sale, $client, $type);

        return $cdr->getContent();
    }

    public function pdfNote($id)
    {
        $folder_client = auth()->user()->headquarter->client->document;
        $credit_note = CreditNote::find($id);

        $file = $credit_note->type_voucher->code . '-' . $credit_note->serial_number . '-' . $credit_note->correlative;
        $file_path = 'pdf/' . $folder_client . '/' . $file . '.pdf';
        return response()->json($file_path);
    }

    public function pdfNoteDebit($id)
    {
        $folder_client = auth()->user()->headquarter->client->document;
        $debit_note = DebitNote::find($id);
        $file = $debit_note->type_voucher->code . '-' . $debit_note->serial_number . '-' . $debit_note->correlative;
        $file_path = 'pdf/' . $folder_client . '/' . $file . '.pdf';
        return response()->json($file_path);
    }

    /**
     * Sales
     */
    public function showPdfSale($correlative, $serial_number, $type_voucher)
    {
        $folder_client = auth()->user()->headquarter->client->document;
        $file = $serial_number . '-' . $correlative . '.pdf';
        $file_path = 'pdf/' . $folder_client . '/' . $file;
        $pdf = Storage::disk('public')->get($file_path);
        return $pdf;
    }

    public function showXmlSale($correlative, $serial_number, $type_voucher)
    {
        // $folder_client = $this->getFolderClient();
        $folder_client = auth()->user()->headquarter->client->document;
        $file = $folder_client . '-' . $type_voucher . '-' . $serial_number . '-' . $correlative . '.xml';
        $file_path = 'xml/' . $folder_client . '/' . $file;
        $xml = Storage::disk('public')->get($file_path);
        return $xml;
    }

    public function searchFile(Request $request)
    {
        return response()->json(Storage::disk('public')->exists(str_replace('/storage/', '', $request->get('file_path'))));
    }

    public function downloadPdfSale($id)
    {
        $quotation = Quotation::find($id);
        $quotation_detail = QuotationDetail::where('quotation_id', $id)->get();
        $data = array(
            'quotation' => $quotation,
            'quotation_detail' => $quotation_detail
        );
        $pdf = PDF::loadView('commercial.quotation.pdf', $data)->setPaper('a4');
        return $pdf->download('Cotización_' . date('d-m-Y') . '.pdf');
    }

    public function sendSale(Request $request)
    {
        try {

            $quotation = Quotation::find($request->post('quotation_id'));
            $quotation_detail = QuotationDetail::where('quotation_id', $request->post('quotation_id'))->get();
            $leyenda = NumerosEnLetras::convertir($quotation->total) . '/100';
            $bankInfo = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->first();
            $clientInfo = Client::find(Auth::user()->headquarter->client_id);
            $data = array(
                'quotation' => $quotation,
                'quotation_detail' => $quotation_detail,
                'clientInfo' => $clientInfo,
                'leyenda' => $leyenda,
                'bankInfo' => $bankInfo
            );
            // dd($clientInfo);
            $pdf = PDF::loadView('commercial.quotation.pdf', $data)->setPaper('a4');

            Mail::to($request->post('email'))
                ->send(
                    new SendQuotation(
                        $request->post('quotation_id'),
                        $pdf,
                        $clientInfo
                    )
                );
            if (Mail::failures()) {
                return response()->json('No se pudo enviar el Correo');
            } else {
                $quotation = Quotation::find($request->post('quotation_id'));
                $quotation->sendemail = 1;
                $quotation->save();
                echo json_encode(true);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
            return response()->json(false);
        }
    }

    public function sendSaleClient(Request $request, $id)
    {
        try {
            $sale = Sale::where('id', $id)->with('coin', 'type_voucher')->first();
            $sale_detail = SaleDetail::where('sale_id', $id)->with('product', 'product.coin')->get();

            $clientInfo = Client::find(Auth::user()->headquarter->client_id);
            $pdf = $this->showPdfSale($sale->correlative, $sale->serialnumber, $sale->type_voucher->code);

            // $pdf = PDF::loadView('commercial.sale.pdf', compact('sale','sale_detail','leyenda', 'bankInfo', 'clientInfo','customerInfo', 'invoice','igv', 'qrCode'))->setPaper('A4');
            //$cdr = public_path('files/cdr/R-'. Auth::user()->headquarter->client->document . '-' . $sale->type_voucher->code . '-' . $sale->serialnumber . '-' . $sale->correlative . '.zip');
            $xml = $this->showXmlSale($sale->correlative, $sale->serialnumber, $sale->type_voucher->code);

            $attach = $xml;

            $email = $sale->customer->email;

            if ($sale->customer->email == null) {
                return response()->json(-5);
            }

            // dd($sale);
            Mail::to($email)
                ->send(
                    new SendSale(
                        $sale,
                        $pdf,
                        $attach,
                        $clientInfo
                    )
                );
            if (Mail::failures()) {
                return response()->json('No se pudo enviar el Correo');
            } else {
                $quotation = Sale::find($id);
                $quotation->sendemail = 1;
                $quotation->save();
                echo json_encode(true);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
            return response()->json(false);
        }
    }

    public function sendNoteClient(Request $request, $id)
    {
        try {
            $cn = CreditNote::find($id);
            $decimal = Str::after($cn->total, '.');
            $int = Str::before($cn->total, '.');
            $leyenda = NumerosEnLetras::convertir($int) . ' con ' . $decimal . '/100';
            $bankInfo = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->first();
            $clientInfo = Client::find(Auth::user()->headquarter->client_id);
            $customerInfo = Customer::find($cn->customer_id);
            $igv = DB::table('taxes')->where('id', '=', 1)->first();
            $credit_note = $cn;


            $pdf = PDF::loadView('commercial.note.pdf', compact('cn', 'credit_note', 'leyenda', 'bankInfo', 'clientInfo', 'customerInfo', 'igv'))->setPaper('A4');
            // $cdr = public_path('files/cdr/R-'. Auth::user()->headquarter->client->document . '-' . $cn->type_voucher->code . '-' . $cn->serial_number . '-' . $cn->correlative . '.zip');
            // $xml = public_path('files/xml/'. Auth::user()->headquarter->client->document . '-' . $cn->type_voucher->code . '-' . $cn->serial_number . '-' . $cn->correlative . '.xml');

            $attach = array();

            $email = $cn->customer->email;

            if ($cn->customer->email == null) {
                return response()->json('El cliente no tiene una cuenta de correo configurada.');
            }

            // dd($sale);
            Mail::to($email)
                ->send(
                    new SendNoteCredit(
                        $cn,
                        $pdf,
                        $attach,
                        $clientInfo
                    )
                );
            if (Mail::failures()) {
                return response()->json('No se pudo enviar el Correo');
            } else {
                $quotation = CreditNote::find($id);
                $quotation->send_customer = 1;
                $quotation->save();
                echo json_encode(true);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
            return response()->json(false);
        }
    }

    public function sendNoteDebitClient(Request $request, $id)
    {
        try {
            $cn = DebitNote::find($id);
            $decimal = Str::after($cn->total, '.');
            $int = Str::before($cn->total, '.');
            $leyenda = NumerosEnLetras::convertir($int) . ' con ' . $decimal . '/100';
            $bankInfo = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->first();
            $clientInfo = Client::find(Auth::user()->headquarter->client_id);
            $customerInfo = Customer::find($cn->customer_id);
            $igv = DB::table('taxes')->where('id', '=', 1)->first();
            $debit_note = $cn;

            $pdf = PDF::loadView('commercial.note.pdfdebit', compact('cn', 'debit_note', 'leyenda', 'bankInfo', 'clientInfo', 'customerInfo', 'igv'))->setPaper('A4');
            // $cdr = public_path('files/cdr/R-'. Auth::user()->headquarter->client->document . '-' . $cn->type_voucher->code . '-' . $cn->serial_number . '-' . $cn->correlative . '.zip');
            // $xml = public_path('files/xml/'. Auth::user()->headquarter->client->document . '-' . $cn->type_voucher->code . '-' . $cn->serial_number . '-' . $cn->correlative . '.xml');

            $attach = array();

            $email = $cn->customer->email;

            if ($cn->customer->email == null) {
                return response()->json('El Cliente no tiene una cuenta de email configurada.');
            }

            // dd($sale);
            Mail::to($email)
                ->send(
                    new SendNoteDebit(
                        $cn,
                        $pdf,
                        $attach,
                        $clientInfo
                    )
                );
            if (Mail::failures()) {
                return response()->json('No se pudo enviar el Correo');
            } else {
                $quotation = DebitNote::find($id);
                $quotation->send_customer = 1;
                $quotation->save();
                echo json_encode(true);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
            return response()->json(false);
        }
    }

    /**
     * QrCode
     */
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
        $content = implode('|', $params) . '|';

        return $content . $hash;
    }

    private function getQrImage($content)
    {
        $renderer = new Png();
        $renderer->setHeight(120);
        $renderer->setWidth(120);
        $renderer->setMargin(0);
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($content, 'UTF-8', ErrorCorrectionLevel::Q);

        return $qrCode;
    }

    public function noteindex()
    {
        return view('commercial.sale.notes.index');
    }

    public function dt_notes(Request $request)
    {
        $notes = CreditNote::with('customer', 'type_voucher', 'sale', 'sunat_code')
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->where(function ($query) {
                if (
                    auth()->user()->hasRole('admin') ||
                    auth()->user()->hasRole('superadmin') ||
                    auth()->user()->hasRole('manager')
                ) {
                } else {
                    $query->where('user_id', Auth::id());
                }
            })
            ->get();
        return datatables()->of($notes)->toJson();
    }

    public function dt_notesDebit(Request $request)
    {
        return datatables()->of(
            Db::table('debit_notes')
                ->join('customers', 'debit_notes.customer_id', '=', 'customers.id')
                ->join('typevouchers', 'debit_notes.typevoucher_id', '=', 'typevouchers.id')
                ->join('sales', 'debit_notes.sale_id', 'sales.id')
                ->leftJoin('sunat_codes', 'debit_notes.response_sunat', '=', 'sunat_codes.id')
                ->where('debit_notes.client_id', auth()->user()->headquarter->client_id)
                ->where(function ($query) use ($request) {
                    if ($request->get('denomination') != '') {
                        $query->where('customers.description', 'like', '%' . $request->get('denomination') . '%');
                    }

                    if ($request->get('serial') != '') {
                        $query->where('debit_notes.serial_number', 'like', '%' . $request->get('serial') . '%');
                    }

                    if (
                        auth()->user()->hasRole('admin') ||
                        auth()->user()->hasRole('superadmin') ||
                        auth()->user()->hasRole('manager')
                    ) {
                    } else {
                        $query->where('user_id', Auth::id());
                    }
                })
                ->get([
                    'debit_notes.correlative',
                    'debit_notes.serial_number',
                    'customers.document',
                    'customers.description as c_description',
                    'sales.serialnumber as ss',
                    'sales.correlative as sc',
                    'debit_notes.total',
                    'debit_notes.id',
                    'debit_notes.status as status',
                    'typevouchers.code as tp_description',
                    'debit_notes.status_sunat',
                    'sunat_codes.description',
                    'sunat_codes.code as sunat_code',
                    'debit_notes.response_sunat',
                ])
        )->toJson();
    }

    public function createNote($id, $type)
    {
        if ($type == '01') {
            $tv = 4;
        } elseif ($type == '03') {
            $tv = 3;
        }

        $date = date('d-m-Y');
        $sale = Sale::with('type_voucher')->where('id', $id)->first();

        $typeoperations = TypeOperation::get(['id', 'operation']);
        $bankInfo = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '3')->first();
        $correlative = DB::table('correlatives')->where([
            ['headquarter_id', '=', $this->headquarter],
            ['typevoucher_id', '=', $tv],
            ['contingency', '0']
        ])->first();

        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $sale = Sale::with('type_voucher')->where('id', $id)->first();
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

        $bankAccounts = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '!=','3')->get();
        $paymentMethods = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)->get(['name', 'id']);

        $data = array(
            'sale' => $sale,
            'sale_detail' => SaleDetail::with('product', 'product.tax')->where('sale_id', $id)->get(),
            'customer' => Customer::find($sale->customer_id),
            'typedocuments' => $this->_ajax->getTypeDocuments(),
            'coin' => Coin::find($sale->coin_id),
            'products' => $this->_ajax->getProducts(),
            'igv' => $this->_ajax->getIgv(),
            'correlative' => $correlative,
            'currentDate' => $date,
            'currentDateLast' => date('d-m-Y', strtotime('+7 day', strtotime($date))),
            'type' => $type,
            'categories' => Category::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'operations_type' => OperationType::all(),
            'brands' => Brand::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'clientInfo' => $clientInfo,
            'bankInfo' => $bankInfo,
            'type_credit_notes'  =>  TypeCreditNote::where('id', '!=', 10)->get(),
            'type_operation'    =>  TypeOperation::find($sale->typeoperation_id),
            'igvType'   => IgvType::all(),
            'type_voucher'      =>  TypeVoucher::find($sale->typevoucher_id),
            'cashes' => $cashes,
            'bankAccounts' => $bankAccounts,
            'paymentMethods' => $paymentMethods
        );
        return view('commercial.note.credit')->with($data);
    }

    public function createNoteDebit($id, $type)
    {
        if ($type == '01') {
            $tv = 6;
        } elseif ($type == '03') {
            $tv = 5;
        }
        $date = date('d-m-Y');
        $sale = Sale::with('type_voucher')->where('id', $id)->first();

        $typeoperations = TypeOperation::get(['id', 'operation']);
        $bankInfo = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '3')->first();
        $correlative = DB::table('correlatives')->where([
            ['headquarter_id', '=', $this->headquarter],
            ['typevoucher_id', '=', $tv],
            ['contingency', '0']
        ])->first();

        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $data = array(
            'sale' => $sale,
            'sale_detail' => SaleDetail::with('product')->where('sale_id', $id)->get(),
            'customer' => Customer::find($sale->customer_id),
            'typedocuments' => $this->_ajax->getTypeDocuments(),
            'coin' => Coin::find($sale->coin_id),
            'products' => $this->_ajax->getProducts(),
            'igv' => $this->_ajax->getIgv(),
            'correlative' => $correlative,
            'currentDate' => $date,
            'currentDateLast' => date('d-m-Y'),
//            'currentDateLast'   =>  date('d-m-Y', strtotime('+7 day', strtotime($date))),
            'type' => $type,
            'categories' => Category::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'operations_type' => OperationType::all(),
            'brands' => Brand::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'clientInfo' => $clientInfo,
            'bankInfo' => $bankInfo,
            'type_debit_notes' => TypeDebitNote::all(),
            'type_operation' => TypeOperation::find($sale->typeoperation_id),
            'igvType' => IgvType::all(),
            'type_voucher' => TypeVoucher::find($sale->typevoucher_id),
        );

        return view('commercial.note.debit')->with($data);
    }

    public function saveNote(Request $request)
    {
        $igv_percentage = Auth::user()->headquarter->client->igv_percentage;
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;
        $motiveDiscount = [1, 2, 6, 7, 10];

        DB::beginTransaction();
        try {
            if ($request->type == '01') {
                $tv = 4;
            } elseif ($request->type == '03') {
                $tv = 3;
            };

            $correlatives = Correlative::where([
                ['serialnumber', $request->serialnumber],
                ['headquarter_id', $this->headquarter],
                ['typevoucher_id', $tv],
                ['contingency', '0']
            ])->first();

            if ($correlatives == null) {
                return response()->JSON(-9);
            }

            $setCorrelative = (int)$correlatives->correlative + 1;
            $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
            $final = str_repeat('0', ($repeat >= 0) ? $repeat : 0) . $setCorrelative;

            $correlative = Correlative::find($correlatives->id);
            $correlative->correlative = $final;
            $correlative->save();

            $credit_note = new CreditNote;
            $credit_note->serial_number = $correlatives->serialnumber;
            $credit_note->correlative = $final;
            $credit_note->recharge = $request->recharge;
            $credit_note->exonerated = $request->post('c_exonerated');
            $credit_note->unaffected = $request->post('c_unaffected');
            $credit_note->free = $request->post('c_free');
            $credit_note->othercharge = 0.00;
            $credit_note->discount = 0.00;
            $credit_note->icbper = $request->has('c_t') ? $request->post('c_t') : 0.00;
            $credit_note->taxed = $request->post('c_taxed');
            $credit_note->igv = $request->post('c_igv');
            $credit_note->total = $request->post('c_total');
            $credit_note->date_issue = date('Y-m-d', strtotime($request->post('date_issue')));
            $credit_note->due_date = date('Y-m-d', strtotime($request->post('due_date')));
            $credit_note->observation = $request->post('observation');
            $credit_note->sale_id = $request->post('sale_id');
            $credit_note->user_id = Auth::user()->id;
            $credit_note->customer_id = $request->post('customer');
            $credit_note->typevoucher_id = $tv;
            $credit_note->headquarter_id = $this->headquarter;
            $credit_note->client_id = Auth::user()->headquarter->client_id;
            $credit_note->type_credit_note_id = $request->post('type_credit_note');
            if ($request->has('new_serie_related') && $request->has('new_correlative_related')) {
                $credit_note->new_serie_related = $request->new_serie_related;
                $credit_note->new_correlative_related = $request->new_correlative_related;
            }

            $credit_note->condition_payment = $request->condition;
            if ($request->condition == 'EFECTIVO') {
                $credit_note->cash_id = $request->cash;
            }

            if ($request->condition == 'DEPOSITO EN CUENTA') {
                $credit_note->bank_account_id = $request->bank;
            }

            if ($request->condition == 'TARJETA DE CREDITO' || $request->condition == 'TARJETA DE DEBITO') {
                $credit_note->payment_method_id = $request->mp;
            }

            $credit_note->save();

            if ($request->type_credit_note != 4 && !$request->has('product_discount')) {
                for ($x = 0; $x < count($request->post('cd_quantity')); $x++) {
                    $price_unit = $request['cd_price'][$x] / (($igv_percentage / 100) + 1);
                    $producto = Product::find($request->post('product')[$x]);

                    $credit_note_detail = new CreditNoteDetail;
                    $credit_note_detail->quantity = $request->post('cd_quantity')[$x];
                    $credit_note_detail->price = $request->post('cd_price')[$x];
                    $credit_note_detail->subtotal = $request->post('cd_subtotal')[$x];
                    $credit_note_detail->total = $request->post('cd_total')[$x];
                    $credit_note_detail->product_id = $request->post('product')[$x];
                    $credit_note_detail->type_igv_id = $request->post('type_igv')[$x];
                    $credit_note_detail->credit_note_id = $credit_note->id;
                    $credit_note_detail->igv_percentage = $igv_percentage;
                    $credit_note_detail->price_unit = $price_unit;
                    if (isset($request->post('new_description')[$x]) && $request->post('type_credit_note') == 3) {
                        $credit_note_detail->new_description = $request->post('new_description')[$x];
                    }
                    $credit_note_detail->save();

                    if (in_array($request->type_credit_note, $motiveDiscount)) {
                        $store = Store::where('product_id', $request->post('product')[$x])->first();
                        $store->stock = $store->stock + $request->post('cd_quantity')[$x];
                        $store->save();

                        $kardex = new Kardex;
                        $kardex->number = $correlatives->serialnumber . '-' . $final;
                        $kardex->type_transaction = 'Nota de Crédito';
                        $kardex->entry = (int)$request->post('cd_quantity')[$x];
                        $kardex->cost = $producto->cost;
                        $kardex->balance = (int)$store->stock;
                        $kardex->warehouse_id = $mainWarehouseId;
                        $kardex->client_id = auth()->user()->headquarter->client_id;
                        $kardex->product_id = $request->post('product')[$x];
                        $kardex->save();
                    }
                }
            } else {
                $credit_note_detail = new CreditNoteDetail;
                $credit_note_detail->quantity = 1;
                $credit_note_detail->price = $request->post('discount_total');
                $credit_note_detail->subtotal = $request->post('c_taxed');
                $credit_note_detail->total = $request->post('c_total');
                $credit_note_detail->product_id = $request->post('product_discount');
                $credit_note_detail->type_igv_id = 1;
                $credit_note_detail->credit_note_id = $credit_note->id;
                $credit_note_detail->igv_percentage = 18;
                $credit_note_detail->price_unit = $request->post('c_total') / 1.18;
                $credit_note_detail->new_description = "DESCUENTO GLOBAL";
                $credit_note_detail->save();
            }

            $sale = Sale::find($request->post('sale_id'));
            $sale->credit_note_id = $credit_note->id;
            $sale->update();

            if ($sale->condition_payment == 'EFECTIVO') {
                $cash = Cash::where('id', $sale->cash_id)->where('status', 1)->where('client_id', auth()->user()->headquarter->client_id)->first();

                if ($cash != null) {
                    $movement = new CashMovements;
                    $movement->movement = 'ANULACION';
                    $movement->amount = "-{$credit_note->total}";
                    $movement->observation = "{$sale->serialnumber}-{$sale->correlative}";
                    $movement->cash_id = $cash->id;
                    $movement->user_id = auth()->user()->id;
                    $movement->save();
                }
            }

            Session::flash('idd', null);
            Session::flash('id', $credit_note->id);

            DB::commit();

            $sn = true;
            // dd($sn);
            // if($sale->typevoucher_id == 1) {
            AppServiceProvider::constructCreditNote($credit_note);
            // } else {
            //     $sn['response'] = 99;
            // }

            return response()->JSON($sn);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            $rpta = '';
            switch ($e->getCode()) {
                default:
                    $rpta = $e->getMessage();
                    break;
            }
            //dd($e->getMessage());

            return response()->JSON($rpta);
        }
    }

    public function getNote(Request $request)
    {
        $cn = CreditNote::with('type_voucher')->where('id', $request->nc_id)->first();
        return response()->json($cn);
    }

    public function getDebitNote(Request $request)
    {
        $cn = DebitNote::with('type_voucher')->where('id', $request->nd_id)->first();
        return response()->json($cn);
    }

    public function saveNoteDebit(Request $request)
    {
        // DB::beginTransaction();
        // try {
        $igv_percentage = Auth::user()->headquarter->client->igv_percentage;
        if ($request->type == '01') {
            $tv = 6;
        } elseif ($request->type == '03') {
            $tv = 5;
        }

        $correlatives = Correlative::where([
            ['serialnumber', $request->serialnumber],
            ['headquarter_id', $this->headquarter],
            ['typevoucher_id', $tv],
            ['contingency', '0']
        ])->first();

        if ($correlatives == null) {
            return response()->JSON(-9);
        }

        $setCorrelative = (int)$correlatives->correlative + 1;
        $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
        $final = str_repeat('0', ($repeat >= 0) ? $repeat : 0) . $setCorrelative;

        $correlative = Correlative::find($correlatives->id);
        $correlative->correlative = $final;
        $correlative->save();

        $credit_note = new DebitNote;
        $credit_note->serial_number = $correlatives->serialnumber;
        $credit_note->correlative = $final;
        $credit_note->exonerated = $request->post('c_exonerated');
        $credit_note->recharge = $request->recharge;
        $credit_note->unaffected = $request->post('c_unaffected');
        $credit_note->free = $request->post('c_free');
        $credit_note->othercharge = 0.00;
        $credit_note->discount = 0.00;
        $credit_note->icbper = $request->has('c_t') ? $request->post('c_t') : 0.00;
        $credit_note->taxed = (float)$request->post('c_taxed');
        $credit_note->igv = (float)$request->post('c_igv');
        $credit_note->total = (float)$request->post('c_total');
        $credit_note->date_issue = date('Y-m-d', strtotime($request->post('date_issue')));
        $credit_note->due_date = date('Y-m-d', strtotime($request->post('due_date')));
        $credit_note->observation = $request->post('observation');
        $credit_note->sale_id = $request->post('sale_id');
        $credit_note->user_id = Auth::user()->id;
        $credit_note->customer_id = $request->post('customer');
        $credit_note->typevoucher_id = $tv;
        $credit_note->headquarter_id = $this->headquarter;
        $credit_note->client_id = Auth::user()->headquarter->client_id;
        $credit_note->type_debit_note_id = $request->post('type_credit_note');
        if ($credit_note->save()) {
            for ($x = 0; $x < count($request->post('cd_quantity')); $x++) {
                $price_unit = $request['cd_price'][$x] / (($igv_percentage / 100) + 1);
                $credit_note_detail = new DebitNoteDetail;
                $credit_note_detail->quantity = $request->post('cd_quantity')[$x];
                $credit_note_detail->price = $request->post('cd_price')[$x];
                $credit_note_detail->subtotal = $request->post('cd_subtotal')[$x];
                $credit_note_detail->total = (float)$request->post('cd_total')[$x];
                $credit_note_detail->product_id = $request->post('product')[$x];
                // $credit_note_detail->type_igv_id    =   $request->post('type_igv')[$x];
                $credit_note_detail->debit_note_id = $credit_note->id;
                $credit_note_detail->igv_percentage = $igv_percentage;
                $credit_note_detail->price_unit = $price_unit;
                $credit_note_detail->save();
            }
        }

        $sale = Sale::find($request->post('sale_id'));
        $sale->debit_note_id = $credit_note->id;
        $sale->update();

        Session::flash('idd', $credit_note->id);
        Session::flash('id', null);

        DB::commit();

        $sn = true;
        // if($sale->typevoucher_id == 1) {
        AppServiceProvider::constructDebitNote($credit_note);
        // } else {
        //     $sn['response'] = 99;
        // }

        return response()->JSON($sn);
    }

    public function sendLowCommunicationOnly($id)
    {
        $state = AppServiceProvider::constructLowCommunication($id);

        return response()->json($state);
    }

    public function sendLowCommunication(Request $request)
    {
        if ($request->post('type') === '1') {
            $document = Sale::find($request->post('id'));
            $date = $document->date;
            $document->status_condition = 9;
        } else if ($request->post('type') === '2') {
            $document = CreditNote::find($request->post('id'));
            $date = $document->date_issue;
        } else {
            $document = DebitNote::find($request->post('id'));
            $date = $document->date_issue;
        }

        $low = new LowCommunication;
        $correlatives = Correlative::where([
            ['client_id', Auth::user()->headquarter->client_id],
            ['typevoucher_id', 19],
            ['contingency', '0']
        ])->first();

        $setCorrelative = (int)$correlatives->correlative + 1;
        $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
        $final = str_repeat('0', ($repeat >= 0) ? $repeat : 0) . $setCorrelative;

        $correlative = Correlative::findOrFail($correlatives->id);
        $correlative->correlative = $final;
        $correlative->save();

        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;

        DB::beginTransaction();
        try {
            $low->correlative = $final;
            $low->generation_date = $date;
            $low->communication_date = Carbon::now();
            $low->headquarter_id = $this->headquarter;
            $low->client_id = Auth::user()->headquarter->client_id;
            $low->user_id = Auth::user()->id;
            $low->save();

            $low_detail = new LowCommunicationDetail;
            $low_detail->motive = $request->post('motive');
            $low_detail->low_communication_id = $low->id;

            if ($request->post('type') === '1') {
                $low_detail->sale_id = $document->id;

                $mainWarehouse = Warehouse::where('headquarter_id', $document->headquarter_id)->first();
                $mainWarehouseId = $mainWarehouse->id;

                $sale_detail = SaleDetail::where('sale_id', $document->id)->get();
                foreach ($sale_detail as $sd) {
                    $store = Store::where('product_id', $sd->product_id)
                        ->where('warehouse_id', $mainWarehouseId)
                        ->first();
                    $store->stock = $store->stock + $sd->quantity;

                    $kardex = new Kardex;
                    $kardex->number = $final;
                    $kardex->type_transaction = 'Comunicación de Baja';
                    $kardex->entry = (int)$sd->quantity;
                    $kardex->balance = (int)$store->stock;
                    $kardex->cost = $store->product->cost;
                    $kardex->warehouse_id = $mainWarehouseId;
                    $kardex->client_id = auth()->user()->headquarter->client_id;
                    $kardex->product_id = $sd->product_id;
                    $kardex->save();

                    $store->save();
                }

                if ($document->condition_payment == 'EFECTIVO') {
                    if ($document->issue == date('Y-m-d')) {
                        $cash = Cash::where('id', $document->cash_id)->where('status', 1)->where('client_id', auth()->user()->headquarter->client_id)->first();

                        if ($cash != null) {
                            $movement = new CashMovements;
                            $movement->movement = 'ANULACION';
                            $movement->amount = "-{$document->total}";
                            $movement->observation = "{$document->serialnumber}-{$document->correlative}";
                            $movement->cash_id = $cash->id;
                            $movement->user_id = auth()->user()->id;
                            $movement->save();
                        }
                    }
                }
            } else if ($request->post('type') === '2') {
                $low_detail->credit_note_id = $document->id;
            } else {
                $low_detail->debit_note_id = $document->id;
            }

            $low_detail->save();

            $data = array(
                'document_type' => $document->type_voucher->code,
                'serial_number' => $document->serialnumber ? $document->serialnumber : $document->serial_number,
                'correlative' => $document->correlative,
                'motive' => $request->post('motive'),
                'low_correlative' => $correlative->correlative,
                'date_generation' => Carbon::now(),
                'date_communication' => Carbon::now()
            );

            $document->low_communication_id = $low->id;
            $document->save();

            DB::commit();

            AppServiceProvider::constructLowCommunication($low->id);

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            //return response()->json(false);
            dd($e);
        }
    }

    public function dtLows()
    {
        return datatables()->of(
            LowCommunication::with('detail',
                'detail.sale',
                'detail.credit_note',
                'detail.debit_note',
                'detail.sale.type_voucher',
                'detail.credit_note.type_voucher',
                'detail.debit_note.type_voucher',
                'sunat'
            )->where('headquarter_id', $this->headquarter)->get()
        )->editColumn('generation_date', function ($dt) {
            return date('d-m-Y', strtotime($dt->generation_date));
        })->toJson();
    }

    public function showPdfLow($id)
    {
        $low = LowCommunication::find($id);
        $low_detail = LowCommunicationDetail::with('sale',
            'credit_note',
            'debit_note',
            'sale.type_voucher',
            'credit_note.type_voucher',
            'debit_note.type_voucher'
        )
            ->where('low_communication_id', $id)->get();
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);


        $pdf = PDF::loadView('commercial.low.pdf',
            compact('clientInfo', 'low', 'low_detail'))->setPaper('A4');

        return $pdf->stream('COMUNICACIÓN DE BAJA ' . $low->serialnumber . '-' . $low->correlative . '.pdf');
    }

    public function summary()
    {
        return view('commercial.summary.index');
    }

    public function enviarResumen(Request $request, $date)
    {
        $date = date('Y-m-d', strtotime($date));

        return response()->json($this->sendSummary($date));
    }

    public function sendSummary($date)
    {
        $date = date('Y-m-d', strtotime($date));
        try {
            $clients = Client::where('status', 1)->get();

            foreach ($clients as $c) {
                if ($c->type_send_boletas == 1) {
                    $payments = Sale::with('customer', 'customer.document_type', 'type_voucher', 'credit_note.type_voucher')
                        ->where('issue', '=', $date)
                        ->where('typevoucher_id', 2)
                        ->where('client_id', $c->id)
                        ->where('status_sunat', 1)
                        ->where('status', 3)
                        ->get();
                } else {
                    $payments = Sale::with('customer', 'customer.document_type', 'type_voucher', 'credit_note.type_voucher')
                        ->where('issue', '=', $date)
                        ->where('typevoucher_id', 2)
                        ->where('client_id', $c->id)
                        ->whereNull('response_sunat')
                        ->whereNull('status_sunat')
                        ->get();
                }

                $correlative = Correlative::where('client_id', $c->id)
                    ->where('typevoucher_id', 22)
                    ->first();

                $correlative = $correlative->correlative + 1;

                if ($payments->count() > 0) {
                    $summary = new Summary;
                    $summary->correlative = $correlative;
                    $summary->date_issues = $date;
                    $summary->date_generation = $date;
                    $summary->client_id = $c->id;
                    $summary->user_id = 1;
                    $summary->save();

                    foreach ($payments as $p) {
                        $pa = Sale::find($p->id);
                        $pa->status_sunat = 3;
                        $pa->save();

                        if ($c->type_send_boletas == 1) {
                            $summary_detail = new SummaryDetail;
                            $summary_detail->sale_id = $p->id;
                            $summary_detail->condition = '3';
                            $summary_detail->summary_id = $summary->id;
                            $summary_detail->save();
                        } else {
                            $summary_detail = new SummaryDetail;
                            $summary_detail->sale_id = $p->id;
                            $summary_detail->condition = '1';
                            $summary_detail->summary_id = $summary->id;
                            $summary_detail->save();
                            if ($p->status == 3) {
                                $summary_detail = new SummaryDetail;
                                $summary_detail->sale_id = $p->id;
                                $summary_detail->condition = '3';
                                $summary_detail->summary_id = $summary->id;
                                $summary_detail->save();
                            }
                        }
                    }

                    Correlative::where([
                        ['client_id', $c->id],
                        ['typevoucher_id', 22]
                    ])->increment('correlative', 1);

                    AppServiceProvider::constructSummary($summary->id, $c->id);
                }
            }

            DB::commit();
            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(false);
        }
    }

    public function sendSummaryO($id, $condition)
    {
        $client = auth()->user()->headquarter->client_id;

        $state = AppServiceProvider::constructSummary($id, $client);

        return response()->json($state);
    }

    public function disableVoucher($id, $type)
    {

        if ($type == 2) {
            $voucher = Sale::find($id);
            $detail = SaleDetail::where('sale_id', $id)->get();
            $mainWarehouse = Warehouse::where('headquarter_id', $voucher->headquarter_id)->first();
            $mainWarehouseId = $mainWarehouse->id;

            foreach ($detail as $d) {
                $store = Store::where('product_id', $d->product_id)->first();
                $store->stock = $store->stock + $d->quantity;

                $kardex = new Kardex;
                $kardex->number = $voucher->serialnumber . '-' . $voucher->correlative;
                $kardex->type_transaction = 'Comunicación de Baja';
                $kardex->entry = (float)$d->quantity;
                $kardex->balance = (float)$store->stock;
                $kardex->cost = $store->product->cost;
                $kardex->warehouse_id = $mainWarehouseId;
                $kardex->client_id = auth()->user()->headquarter->client_id;
                $kardex->product_id = $d->product_id;
                $kardex->save();

                $store->save();
            }

            if ($voucher->condition_payment == 'EFECTIVO') {
                if ($voucher->issue == date('Y-m-d')) {
                    $cash = Cash::where('user_id', auth()->user()->id)->where('status', 1)->where('client_id', auth()->user()->headquarter->client_id)->first();

                    if ($cash != null) {
                        $movement = new CashMovements;
                        $movement->movement = 'ANULACION';
                        $movement->amount = "-{$voucher->total}";
                        $movement->observation = "{$voucher->serialnumber}-{$voucher->correlative}";
                        $movement->cash_id = $cash->id;
                        $movement->user_id = auth()->user()->id;
                        $movement->save();
                    }
                }
            }
        } else {
            $voucher = CreditNote::find($id);
        }

        $voucher->status = 3;
        $voucher->status_condition = 9;
        return response()->json($voucher->save());
    }

    public function showSummaryPdf($id)
    {
        /*$util = \Util::getInstance();
        $sale = Summary::where('id', $id)->with('type_voucher', 'customer')->first();
        $invoice = $this->convertSummary($id);
        $hash = $util->getHash($invoice);*/

        $summary = Summary::with('detail.sale.type_voucher', 'detail.sale.credit_note.type_voucher')->where('id', $id)->first();
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $pdf = PDF::loadView('commercial.summary.pdf', compact('summary', 'clientInfo'))->setPaper('A4');
        return $pdf->stream('RESUMEN DIARIO RC' . '-' . $clientInfo->document . '.pdf');
    }

    public function getSummaryNotSend($date)
    {
        $sales = Sale::where([
            ['typevoucher_id', 2],
            ['issue', $date],
            ['response_sunat', '!=', 1]
        ])->first();

        return response()->json($sales);
    }

    public function dt_sales_report(Request $request)
    {
        return datatables()->of(
            Db::table('sales')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->join('typevouchers', 'sales.typevoucher_id', '=', 'typevouchers.id')
                ->join('coins', 'sales.coin_id', '=', 'coins.id')
                ->leftJoin('sunat_codes', 'sales.response_sunat', '=', 'sunat_codes.id')
                ->where('sales.headquarter_id', auth()->user()->headquarter_id)
                ->where(function ($query) use ($request) {
                    if ($request->get('since') != '' && $request->get('until') != '') {
                        $query->whereBetween('sales.date', [
                            date('Y-m-d', strtotime($request->get('since'))),
                            date('Y-m-d', strtotime($request->get('until')))
                        ]);
                    }
                })
                ->get([
                    'sales.date',
                    'sales.correlative',
                    'sales.serialnumber',
                    'customers.document',
                    'typevouchers.code',
                    'customers.description as c_description',
                    'coins.symbol',
                    'sales.total',
                    'sales.id',
                    'sales.sendemail',
                    'sales.status as status',
                    'typevouchers.code',
                    'typevouchers.description as tp_description',
                    'sales.quotation_id',
                    'sales.status_sunat',
                    'sunat_codes.description',
                    'sunat_codes.code as sunat_code',
                    'sales.response_sunat',
                    'sales.credit_note_id',
                    'sales.debit_note_id',
                    'sales.low_communication_id'
                ])
        )->toJson();
    }
}
