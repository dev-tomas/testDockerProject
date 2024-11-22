<?php

namespace App\Http\Controllers;

use App\Inventory;
use App\Kardex;
use App\Product;
use App\Store;
use App\Warehouse;
use chillerlan\QRCode\QRCode;
use DB;
use Dompdf\Dompdf;
use PDF;
use Auth;
use App\Sale;
use App\Client;
use App\Ubigeo;
use App\Customer;
use App\Transfer;
use App\SaleDetail;
use App\Correlative;
use App\TypeDocument;
use App\ReferenceGuide;
use Illuminate\Http\Request;
use App\ReferenceGuideDetail;
use App\Providers\AppServiceProvider;
use App\Http\Controllers\SunatController;
use Str;

class ReferenceGuideController extends Controller
{
    public $headquarter;
    public $_ajax;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->middleware('can:comprobantes.guiasremsion')->only(['index', 'dt_guide']);
        $this->middleware('can:guiaremision.create')->only(['create', 'store']);

        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
        $this->_ajax = new AjaxController();
    }

    public function index()
    {
        return view('accounting.referenceGuide.index');
    }

    public function dt_guide(Request $request)
    {
        $reference = ReferenceGuide::with('type_voucher',
            'headquarter',
            'customer',
            'sunat_code',
            'ubigeo_arrival',
            'ubigeo_start')
            ->where(function ($query) use($request) {
                if($request->get('denomination') != ''){
                    $query->whereHas('customer', function ($q) use($request) {
                        $q->where('description', 'like', '%' . $request->get('denomination') . '%');
                    });
                }

                if($request->get('serial') != ''){
                    $query->where('correlative', 'like', '%' . $request->get('serial') . '%');
                }

                if($request->get('dateOne') != ''){
                    $query->whereBetween('date',  [$request->get('dateOne'), $request->get('dateTwo')]);
                }

            })
            ->where('headquarter_id', $this->headquarter)
            ->get();
        return datatables()->of($reference)->toJson();
    }

    public function create(Request $request)
    {
        $correlatives = Correlative::where('headquarter_id', $this->headquarter)->where('typevoucher_id', 7)
                                    ->where('contingency', 0)->where('client_id', auth()->user()->headquarter->client_id)->get();
        $currentDate = \Carbon\Carbon::now()->format('d-m-Y');
        $ubigeo = Ubigeo::all();
        $typedocuments = TypeDocument::all();
        $sales = Sale::where('headquarter_id', $this->headquarter)
                        ->whereNull('low_communication_id')
                        ->whereNull('credit_note_id')
                        ->orderBy('id', 'desc')
                        ->take(50)
                        ->get(['id', 'serialnumber', 'correlative']);
        $sale = null;
        if ($request->has('sale')) {
            $sale = Sale::with('detail')->where('headquarter_id', $this->headquarter)
                        ->where('client_id', auth()->user()->headquarter->client_id)->find($request->sale);
        }
        $products = $this->_ajax->getProducts(2);
        $customers = $this->_ajax->getCustomers();
        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', '!=', $this->headquarter)->get();

        return view('accounting.referenceGuide.create-20', compact('products', 'customers', 'correlatives', 'currentDate', 'typedocuments', 'sales', 'sale', 'ubigeo', 'warehouses'));
    }

    public function getCorrelative($serie)
    {
        $correlatives = Correlative::where('headquarter_id', $this->headquarter)->where('typevoucher_id', 7)
                                    ->where('contingency', 0)->where('client_id', auth()->user()->headquarter->client_id)
                                    ->where('serialnumber', $serie)->first();

        return response()->json($correlatives);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $correlatives = Correlative::where([
                ['client_id', '=', auth()->user()->headquarter->client_id],
                ['headquarter_id', '=', $this->headquarter],
                ['contingency', '=', 0],
                ['serialnumber', '=', $request->serialnumber],
                ['typevoucher_id', 7],
            ])->first();

            $setCorrelative = (int) $correlatives->correlative + 1;
            $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
            $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

            $correlative = Correlative::findOrFail($correlatives->id);
            $correlative->correlative = $final;
            $correlative->save();

            $now = \Carbon\Carbon::now()->format('Y-m-d');

            $tv = 7;

            if ($request->motive == 2) {
                $customerName = auth()->user()->headquarter->client->trade_name;
                $customerDocument = auth()->user()->headquarter->client->document;
                $customerTypeDocument = 4;
            } else {
                $customer = Customer::find($request->customer);

                $customerName = $customer->description;
                $customerDocument = $customer->document;
                $customerTypeDocument = $customer->typedocument_id;

                if ($request->motive != 8) {
                    if ($customerDocument == auth()->user()->headquarter->client->document) {
                        return response()->json(['response' => -45, 'description' => 'Destinatario no debe ser igual al remitente']);
                    }
                }
            }

            $guide = new ReferenceGuide;
            $guide->serialnumber = $request->serialnumber;
            $guide->correlative = $final;
            $guide->date = date('Y-m-d', strtotime($request->date));
            $guide->traslate = date('Y-m-d', strtotime($request->startTraslate));
            $guide->guide_type = 1;
            $guide->motive = $request->motive;
            $guide->modality = (string) $request->modality;
            $guide->weight = $request->weight;
            $guide->weight_measure = $request->weight_measure;
            $guide->receiver = $customerName;
            $guide->receiver_document = $customerDocument;
            $guide->receiver_type_document_id = $customerTypeDocument;
            $guide->start_address = $this->replaceSpecialCharacter(trim($request->start_address));
            $guide->arrival_address = $this->replaceSpecialCharacter(trim($request->arrival_address));
            $guide->arrival_address_ubigeo = $request->arrival_address_ubigeo;
            $guide->start_address_ubigeo = $request->start_address_ubigeo;
            $guide->transport_name = $request->transportName;
            $guide->transport_document = $request->transportDoc;
            $guide->transport_type_document_id = $request->transportTypeDoc;
            $guide->driver_document = $request->driverDoc;
            $guide->driver_name = $request->driverName;
            $guide->vehicle = $request->vehicle;
            $guide->driver_type_document_id = $request->typeDocDriver;
            $guide->client_id = auth()->user()->headquarter->client_id;
            $guide->headquarter_id = $this->headquarter;
            $guide->customer_id = $request->customer;
            $guide->typevoucher_id = $tv;
            $guide->observations = $request->observations;
            $guide->lumps = $request->lumps;
            $guide->weight_measure = $request->weight_measure;
            $guide->driver_firstname = $request->driver_firstname;
            $guide->driver_familyname = $request->driver_familyname;
            $guide->driver_license = $request->driver_license;
            $guide->sunat_code_start = $request->start_code;
            $guide->sunat_code_arrival = $request->arrival_code;
            $guide->warehouse_destination = $request->warehouse_destination;
            $guide->uuid = Str::uuid();
            $guide->sale_id = $request->sale;
            if ($guide->save()) {
                for ($i=0; $i < count($request->cd_product); $i++) {
                    $guideDetail = new ReferenceGuideDetail;
                    $guideDetail->quantity = $request->cd_quantity[$i];
                    $guideDetail->reference_guide_id = $guide->id;
                    $guideDetail->product_id = $request->cd_product[$i];
                    $guideDetail->save();

                    if ($request->motive == 2 || $request->motive == 8) {
                        $this->discountStock($request->cd_quantity[$i], $request->cd_product[$i], $guide->serialnumber, $guide->correlative);

                        $this->addStock($request->cd_quantity[$i], $request->cd_product[$i], $guide->serialnumber, $guide->correlative, $request->warehouse_destination);
                    }
                }
            }

            DB::commit();
            
            AppServiceProvider::constructReferralGuide($guide->id);


            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return $e->getMessage();
        }
    }

    public function consultToApi($id)
    {
        $reference = ReferenceGuide::where('client_id', auth()->user()->headquarter->client_id)
            ->where('uuid', $id)
            ->first();

        if ($reference->ticket == null) {
            return AppServiceProvider::constructReferralGuide($reference->id);
        }

        return AppServiceProvider::consultReferenceApi($reference);
    }

    public function createTransfer(Request $request)
    {
        $transfers = Transfer::whereIn('id', $request->check_s)->get();
        $customers = Customer::where('client_id', auth()->user()->headquarter->client_id)->get();
        $correlatives = Correlative::where('headquarter_id', $this->headquarter)->where('typevoucher_id', 7)
                                    ->where('contingency', 0)->where('client_id', auth()->user()->headquarter->client_id)->get();
        $currentDate = \Carbon\Carbon::now()->format('d-m-Y');
        $ubigeo = Ubigeo::all();
        $typedocuments = TypeDocument::all();

        return view('accounting.referenceGuide.createFromTransfer', compact('customers', 'correlatives', 'currentDate', 'typedocuments', 'transfers', 'ubigeo'));
    }

    public function showPDF($id)
    {
        $guide = ReferenceGuide::with('ubigeo_arrival', 'ubigeo_start')->where('id', $id)->first();
        if ($guide->qr_text != null) {
            $qr = (new QRCode())->render($guide->qr_text);
        } else {
            $qr = null;
        }
        $clientInfo = Client::find(auth()->user()->headquarter->client_id);

        if($clientInfo->reference_guide_size == 'a4') {
            $html = view("accounting.referenceGuide.pdf", compact('guide', 'clientInfo', 'qr'));
        } else {
            $html = view('accounting.referenceGuide.ticket', compact('guide', 'clientInfo', 'qr'));
        }

        $dompdf = new Dompdf();
        $dompdf->set_option('isRemoteEnabled', true);
        if($clientInfo->reference_guide_size == 'a4') {
            $dompdf->set_paper('A4');
        } else {
            $dompdf->set_paper([0, 0, 200, 105]);
        }
        $dompdf->load_html($html);
        $dompdf->render();
        $page_count = $dompdf->get_canvas()->get_page_number();
        unset($dompdf);
        $dompdf = new DOMPDF();
        $dompdf->set_option('isRemoteEnabled', true);
        if($clientInfo->reference_guide_size == 'a4') {
            $dompdf->set_paper('A4');
        } else {
            $dompdf->set_paper([0, 0, 200, 110 * ($page_count) - ($page_count * 32)]);
        }
        $dompdf->load_html($html);
        $dompdf->render();
        return $dompdf->stream('GUIA DE REMISION ' . $guide->serialnumber . '-' . $guide->correlative . '.pdf', ['Attachment' => 0]);
    }

    public function discountStock($quantity, $product, $saleSerie, $saleCorrelative, $tc = null, $coin = 1)
    {
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;

        $producto = Product::find($product);

        $stock = Store::where('product_id',$product)->where('warehouse_id',$mainWarehouseId)->first();
        $oldStock = $stock->stock;

        $newStock = (int) $oldStock - (int) $quantity;
        $stock->stock = $newStock;
        if ($stock->update()) {
            $oldInventary = Inventory::where('product_id', $product)
                ->where('warehouse_id', $mainWarehouseId)
                ->where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', $this->headquarter)->first();
            if ($oldInventary != null) {
                $oldInventaryStock = $oldInventary->amount_entered;
                $oldInventary->amount_entered = (int) $oldInventaryStock - (int) $quantity;
                $oldInventary->update();
            }

            $kardex = new Kardex;
            $kardex->number = $saleSerie . '-' . $saleCorrelative;
            $kardex->type_transaction = 'GUIA DE REMISION';
            $kardex->output = (int) $quantity * -1;
            $kardex->balance = (int) $oldStock - (int) $quantity;
            $kardex->cost = $producto->cost;
            $kardex->warehouse_id = $mainWarehouseId;
            $kardex->client_id = auth()->user()->headquarter->client_id;
            $kardex->product_id = $product;
            $kardex->coin_id = $coin;
            $kardex->exchange_rate = auth()->user()->headquarter->client->exchange_rate_sale;
            $kardex->save();
            return true;
        } else {
            return false;
        }
    }

    public function addStock($quantity, $product, $saleSerie, $saleCorrelative, $warehouseDestination, $tc = null, $coin = 1)
    {
        $mainWarehouse = Warehouse::find($warehouseDestination);

        $producto = Product::find($product);

        $stock = Store::where('product_id',$product)->where('warehouse_id',$mainWarehouse->id)->first();
        if ($stock == null) {
            $stock = new Store;
            $stock->product_id = $product;
            $stock->warehouse_id = $mainWarehouse->id;
            $stock->stock = 0;
            $stock->save();
        }
        $oldStock = $stock->stock;

        $newStock = $oldStock + $quantity;
        $stock->stock = $newStock;
        if ($stock->update()) {
            $oldInventary = Inventory::where('product_id', $product)
                ->where('warehouse_id', $mainWarehouse->id)
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->where('headquarter_id', $this->headquarter)
                ->first();
            if ($oldInventary != null) {
                $oldInventaryStock = $oldInventary->amount_entered;
                $oldInventary->amount_entered = $oldInventaryStock + $quantity;
                $oldInventary->update();
            }

            $kardex = new Kardex;
            $kardex->number = $saleSerie . '-' . $saleCorrelative;
            $kardex->type_transaction = 'GUIA DE REMISION';
            $kardex->entry = $quantity ;
            $kardex->balance = $oldStock + $quantity;
            $kardex->cost = $producto->cost;
            $kardex->warehouse_id = $mainWarehouse->id;
            $kardex->client_id = auth()->user()->headquarter->client_id;
            $kardex->product_id = $product;
            $kardex->coin_id = $coin;
            $kardex->exchange_rate = auth()->user()->headquarter->client->exchange_rate_sale;
            $kardex->save();
            return true;
        } else {
            return false;
        }
    }

    public function replaceSpecialCharacter($string)
    {
        $string = preg_replace("/[\r\n|\n|\r]+/", " ", $string);
        $string = str_replace(
            array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
            array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
            $string
        );

        $string = str_replace(
            array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
            array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
            $string
        );

        $string = str_replace(
            array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
            array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
            $string
        );

        $string = str_replace(
            array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
            array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
            $string
        );

        $string = str_replace(
            array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
            array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
            $string
        );

        $string = str_replace(
            array('Ñ', 'ñ', 'Ç', 'ç', 'N°', 'n°','Nº', '.', '(', ')', '-', '–'),
            array('N', 'n', 'C', 'c', 'N', 'n', 'N',' ', '', '', '', ''),
            $string
        );
        $string = str_replace(
            array('°'),
            array(''),
            $string
        );

        return $string;
    }

    public function getSaleDetail(Request $request)
    {
        $details = SaleDetail::with('product')->where('sale_id', $request->sale)->get();

        $data = array();
        $cont = 0;

        foreach ($details as $detail) {
            $data[$cont]['product_id'] = $detail->product_id;
            $data[$cont]['product'] = "{$detail->product->internalcode} - {$detail->product->description}";
            $data[$cont]['quantity'] = $detail->quantity;
            $data[$cont]['detail'] = $detail->detail != null ? $detail->detail : '';

            $cont++;
        }

        return response()->json($data);
    }
}
