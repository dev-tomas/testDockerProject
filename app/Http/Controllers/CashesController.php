<?php

namespace App\Http\Controllers;

use App\HeadQuarter;
use App\Quotation;
use App\User;
use PDF;
use App\Cash;
use App\Coin;
use App\Sale;
use App\Client;
use Carbon\Carbon;
use Dompdf\Dompdf;
use App\CashMovements;
use App\PaymentCredit;
use App\LiquidationCash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\MovementCashExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class CashesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function index()
    {
        
        $coins = Coin::get(['id', 'description']);
        $clientInfo = Client::where('id', auth()->user()->headquarter->client_id)->first(['exchange_rate_sale', 'id']);
        $users = User::where('client_id', $clientInfo->id)->where('headquarter_id', $this->headquarter)
                        ->where('status', 1)
                        ->get();
        $currentHeadquarter = HeadQuarter::select('description')->find($this->headquarter);

        return view('commercial.cashes.index', compact('coins', 'clientInfo', 'users', 'currentHeadquarter'));
    }

    public function liquidations()
    {
        $cashes = Cash::where('client_id', auth()->user()->headquarter->client_id)
                        ->where('headquarter_id', $this->headquarter)
                        ->where(function($query) {
                            if (! auth()->user()->hasRole('admin') && ! auth()->user()->hasRole('manager') &&
                                ! auth()->user()->hasRole('superadmin')) {
                                $query->where('user_id', auth()->user()->id);
                            }
                        })
                        ->get();
        
        return view('commercial.cashes.liquidations', compact('cashes'));
    }

    public function dt_liquidations(Request $request)
    {
        $cashes = LiquidationCash::with('user', 'cash', 'cash.coin')
                        ->where('client_id', auth()->user()->headquarter->client_id)
                        ->where('headquarter_id', $this->headquarter)
                        ->where(function ($query) use($request) {
                            if($request->get('cash') != ''){
                                $query->where('cash_id', $request->get('cash'));
                            }
                            if($request->get('dates') != ''){
                                $query->where('status', $request->get('dates'));
                            }
                            if($request->get('dateOne') != ''){
                                $query->whereDate('created_at',  '>=', $request->get('dateOne'))
                                        ->whereDate('created_at',  '<=', $request->get('dateTwo'));
                            }
                            if (auth()->user()->headquarter->client->cash_type == 1 &&
                                ! auth()->user()->hasRole('admin') && ! auth()->user()->hasRole('manager') &&
                                ! auth()->user()->hasRole('superadmin')) {
                                $query->where('user_id', auth()->user()->id);
                            }
                        })
                        ->get();

        $data = [];
        $cont = 0;

        foreach ($cashes as $closing) {
            $data[$cont]['id'] = $closing->id;
            $data[$cont]['user'] = $closing->user->name;
            $data[$cont]['cash'] = "{$closing->cash->name}";
            $data[$cont]['total'] = "{$closing->cash->coin->symbol} {$closing->total}";
            $data[$cont]['date'] = date('Y-m-d H:i:s', strtotime($closing->created_at));

            $cont++;
        }

        return datatables()->of($data)->toJson();
    }

    public function showLiquidationsPdf($id)
    {
        $liquidation = LiquidationCash::with('user', 'cash', 'cash.coin')->find($id);
        
		$html = view('commercial.cashes.liquidations-pdf', compact('liquidation'));
	
		$dompdf = new Dompdf();
		$dompdf->set_option('isRemoteEnabled', true);
		$dompdf->set_paper([0, 0, 200, 105]);
		$dompdf->load_html($html);
		$dompdf->render();
		$page_count = $dompdf->get_canvas()->get_page_number();
		unset($dompdf);
		$dompdf = new DOMPDF();
		$dompdf->set_option('isRemoteEnabled', true);
		$dompdf->set_paper([0, 0, 200, 115 * ($page_count) - ($page_count * 32)]);
		$dompdf->load_html($html);
		$dompdf->render();
		$dompdf->stream('CIERRE DE CAJA.pdf', ['Attachment' => 0]);
    }

    public function dt_cashes(Request $request)
    {
        $cashes = Cash::with('coin', 'user:id,name')
                        ->where('client_id', auth()->user()->headquarter->client_id)
                        ->where('headquarter_id', $this->headquarter)
                        ->where(function ($query) use($request) {
                            if($request->get('filter_name') != ''){
                                $query->where('name','like', '%' . $request->get('filter_name') . '%');
                            }
                            if($request->get('filter_status') != ''){
                                $query->where('status', $request->get('filter_status'));
                            }

                            if (auth()->user()->headquarter->client->cash_type == 1 &&
                                ! auth()->user()->hasRole('admin') && ! auth()->user()->hasRole('manager') &&
                                ! auth()->user()->hasRole('superadmin')) {
                                $query->where('user_id', auth()->user()->id);
                            }
                        })
                        ->get();

        return datatables()->of($cashes)->toJson();
    }

    public function storeCash(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->has('cashid')) {
                $cash = Cash::find($request->cashid);
            } else {
                $cash = new Cash;
            }
            $cash->name = $request->name;
            $cash->code = $request->code;
            $cash->coin_id = $request->cash_coin;
            $cash->account = $request->account;
            $cash->user_create = auth()->user()->id;
            $cash->user_update = auth()->user()->id;
            $cash->user_id = $request->user;
            $cash->client_id = auth()->user()->headquarter->client_id;
            $cash->headquarter_id = $this->headquarter;
            $cash->save();

            DB::commit();
            
            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function openCash(Request $request)
    {
        DB::beginTransaction();
        try {
            $typeCash = auth()->user()->headquarter->client->cash_type;

            $userHasCash = Cash::where('client_id', auth()->user()->headquarter->client_id)
                                ->where(function($query) use ($typeCash) {
                                    if ($typeCash == 0) {
                                        $query->where('headquarter_id', $this->headquarter);
                                    } else {
                                        $query->where('user_id', auth()->user()->id);
                                    }
                                })
                                ->where('status', 1)
                                ->first();

            if ($userHasCash != null) {
                return response()->json(-9);
            }

            $cash = Cash::where('id', $request->ci)->where('client_id', auth()->user()->headquarter->client_id)->first();
            $lastClosingAmount = $cash->closing_amount;
            $observation = null;

            if ($lastClosingAmount != null) {
                if ($lastClosingAmount != $request->open_amount) {
                    $observation = 'Apertura con desbalance.';
                }
            }

            $cash->opening_amount = $request->open_amount;
            $cash->status = 1;
            $cash->opening_hour = date('Y-m-d H:i:s');
            $cash->closing_hour = null;
            $cash->user_id = auth()->user()->id;
            $cash->save();

            $movement = new CashMovements;
            $movement->movement = 'Apertura de Caja';
            $movement->amount = $request->open_amount;
            $movement->observation = $observation;
            $movement->cash_id = $cash->id;
            $movement->user_id = $cash->user_id;
            $movement->save();
            
            DB::commit();
            
            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function closeCash(Request $request)
    {
        DB::beginTransaction();
        try {
            $cash = Cash::where('client_id', auth()->user()->headquarter->client_id)
                        ->find($request->ci);
            $cash->closing_amount = $request->close_amount;
            $cash->closing_hour = date('Y-m-d H:i:s');
            $cash->status = 0;
            $cash->save();

            $movement = new CashMovements;
            $movement->movement = 'Cierre de Caja';
            $movement->amount = $request->close_amount;
            $movement->observation = $request->observation;
            $movement->cash_id = $cash->id;
            $movement->user_id = $cash->user_id;
            $movement->save();

            $typeCash = auth()->user()->headquarter->client->cash_type;

            $facturas = Sale::with('credit_note')
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->where('typevoucher_id', 1)
                                ->where('created_at', '>=', $cash->opening_hour)
                                ->where('created_at', '<=', $cash->closing_hour)
                                ->where(function($query) use ($cash, $typeCash) {
                                    if ($typeCash == 0) {
                                        $query->where('headquarter_id', $cash->headquarter_id);
                                    } else {
                                        $query->where('user_id', $cash->user_id);
                                    }
                                })
                                ->orderBy('created_at', 'asc')
                                ->get(['correlative', 'serialnumber', 'total']);

            $boletas = Sale::with('credit_note')
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->where('typevoucher_id', 2)
                                ->where('created_at', '>=', $cash->opening_hour)
                                ->where('created_at', '<=', $cash->closing_hour)
                                ->where(function($query) use ($cash, $typeCash) {
                                    if ($typeCash == 0) {
                                        $query->where('headquarter_id', $cash->headquarter_id);
                                    } else {
                                        $query->where('user_id', $cash->user_id);
                                    }
                                })
                                ->orderBy('created_at', 'asc')
                                ->get(['correlative', 'serialnumber', 'total']);

            $quotations = Quotation::where('headquarter_id', $this->headquarter)
                            ->where('created_at', '>=', $cash->opening_hour)
                            ->where('created_at', '<=', $cash->closing_hour)
                            ->where(function($query) use ($cash, $typeCash) {
                                if ($typeCash == 0) {
                                    $query->where('headquarter_id', $cash->headquarter_id);
                                } else {
                                    $query->where('user_id', $cash->user_id);
                                }
                            })
                            ->where('status', 1)
                            ->where('is_order_note', 1)
                            ->orderBy('created_at', 'asc')
                            ->get(['correlative', 'serial_number', 'total']);

            $transaction = (int) $facturas->count() + (int) $boletas->count() + $quotations->count();
            
            $totalEfectivoGet = Sale::with('credit_note')
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->where('condition_payment', 'EFECTIVO')
                                ->whereNull('low_communication_id')
                                ->where('status', 1)
                                ->where('created_at', '>=', $cash->opening_hour)
                                ->where('created_at', '<=', $cash->closing_hour)
                                ->where(function($query) use ($cash, $typeCash) {
                                    if ($typeCash == 0) {
                                        $query->where('headquarter_id', $cash->headquarter_id);
                                    } else {
                                        $query->where('user_id', $cash->user_id);
                                    }
                                })
                                ->get();

            $totalEfectivo = 0;
            foreach ($totalEfectivoGet as $te) {
                if ($te->credit_note == null) {
                    $totalEfectivo = (float) $totalEfectivo + (float) $te->total;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 1 || 
                            $te->credit_note->type_credit_note_id == 2 || $te->credit_note->type_credit_note_id == 6) {
                    $totalEfectivo = (float) $totalEfectivo + 0;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 4 || 
                $te->credit_note->type_credit_note_id == 5 || $te->credit_note->type_credit_note_id == 7 || $te->credit_note->type_credit_note_id == 9) {
                    $totalEfectivo = (float) $totalEfectivo + (float) $te->credit_note->total;
                }
            }
            $totalTarjetaCreditoGet = Sale::with('credit_note')
                                        ->where('client_id', auth()->user()->headquarter->client_id)
                                        ->where('condition_payment', "TARJETA DE CREDITO")
                                        ->whereNull('low_communication_id')
                                        ->where('status', 1)
                                        ->where('created_at', '>=', $cash->opening_hour)
                                        ->where('created_at', '<=', $cash->closing_hour)
                                        ->where(function($query) use ($cash, $typeCash) {
                                            if ($typeCash == 0) {
                                                $query->where('headquarter_id', $cash->headquarter_id);
                                            } else {
                                                $query->where('user_id', $cash->user_id);
                                            }
                                        })
                                        ->orderBy('created_at', 'asc')
                                        ->get();

            $totalTarjetaCreditoGetOther = Sale::with('credit_note')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->where('other_condition', "TARJETA DE CREDITO")
                ->whereNull('low_communication_id')
                ->where('status', 1)
                ->where('created_at', '>=', $cash->opening_hour)
                ->where('created_at', '<=', $cash->closing_hour)
                ->where(function($query) use ($cash, $typeCash) {
                    if ($typeCash == 0) {
                        $query->where('headquarter_id', $cash->headquarter_id);
                    } else {
                        $query->where('user_id', $cash->user_id);
                    }
                })
                ->orderBy('created_at', 'asc')
                ->get();

            $totalTarjetaCredito = 0;
            foreach ($totalTarjetaCreditoGet as $te) {
                if ($te->credit_note == null) {
                    $totalTarjetaCredito = (float) $totalTarjetaCredito + (float) $te->total;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 1 || 
                    $te->credit_note->type_credit_note_id == 2 || $te->credit_note->type_credit_note_id == 6) {
                    $totalTarjetaCredito = (float) $totalTarjetaCredito + 0;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 4 || 
                $te->credit_note->type_credit_note_id == 5 || $te->credit_note->type_credit_note_id == 7 || $te->credit_note->type_credit_note_id == 9) {
                    $totalTarjetaCredito = (float) $totalTarjetaCredito + (float) $te->credit_note->total;
                }
            }
            $totalTarjetaDebitoGet = Sale::with('credit_note')
                                        ->where('client_id', auth()->user()->headquarter->client_id)
                                        ->where('condition_payment', "TARJETA DE DEBITO")
                                        ->whereNull('low_communication_id')
                                        ->where('status', 1)
                                        ->where('created_at', '>=', $cash->opening_hour)
                                        ->where('created_at', '<=', $cash->closing_hour)
                                        ->where(function($query) use ($cash, $typeCash) {
                                            if ($typeCash == 0) {
                                                $query->where('headquarter_id', $cash->headquarter_id);
                                            } else {
                                                $query->where('user_id', $cash->user_id);
                                            }
                                        })
                                        ->orderBy('created_at', 'asc')
                                        ->get();

            $totalTarjetaDebitoGetOther = Sale::with('credit_note')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->where('other_condition', "TARJETA DE DEBITO")
                ->whereNull('low_communication_id')
                ->where('status', 1)
                ->where('created_at', '>=', $cash->opening_hour)
                ->where('created_at', '<=', $cash->closing_hour)
                ->where(function($query) use ($cash, $typeCash) {
                    if ($typeCash == 0) {
                        $query->where('headquarter_id', $cash->headquarter_id);
                    } else {
                        $query->where('user_id', $cash->user_id);
                    }
                })
                ->orderBy('created_at', 'asc')
                ->get();

            $totalTarjetaDebito = 0;
            foreach ($totalTarjetaDebitoGet as $te) {
                if ($te->credit_note == null) {
                    $totalTarjetaDebito = (float) $totalTarjetaDebito + (float) $te->total;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 1 || 
                    $te->credit_note->type_credit_note_id == 2 || $te->credit_note->type_credit_note_id == 6) {
                    $totalTarjetaDebito = (float) $totalTarjetaDebito + 0;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 4 || 
                $te->credit_note->type_credit_note_id == 5 || $te->credit_note->type_credit_note_id == 7 || $te->credit_note->type_credit_note_id == 9) {
                    $totalTarjetaDebito = (float) $totalTarjetaDebito + (float) $te->credit_note->total;
                }
            }
            $totalDepositoGet = Sale::with('credit_note')
                                    ->where('client_id', auth()->user()->headquarter->client_id)
                                    ->where('condition_payment', 'DEPOSITO EN CUENTA')
                                    ->whereNull('low_communication_id')
                                    ->where('status', 1)
                                    ->where('created_at', '>=', $cash->opening_hour)
                                    ->where('created_at', '<=', $cash->closing_hour)
                                    ->where(function($query) use ($cash, $typeCash) {
                                        if ($typeCash == 0) {
                                            $query->where('headquarter_id', $cash->headquarter_id);
                                        } else {
                                            $query->where('user_id', $cash->user_id);
                                        }
                                    })
                                    ->orderBy('created_at', 'asc')
                                    ->get();

            $totalDepositoGetOther = Sale::with('credit_note')
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->where('other_condition', 'DEPOSITO EN CUENTA')
                ->whereNull('low_communication_id')
                ->where('status', 1)
                ->where('created_at', '>=', $cash->opening_hour)
                ->where('created_at', '<=', $cash->closing_hour)
                ->where(function($query) use ($cash, $typeCash) {
                    if ($typeCash == 0) {
                        $query->where('headquarter_id', $cash->headquarter_id);
                    } else {
                        $query->where('user_id', $cash->user_id);
                    }
                })
                ->orderBy('created_at', 'asc')
                ->get();

            $totalDeposito = 0;
            foreach ($totalDepositoGet as $te) {
                if ($te->credit_note == null) {
                    $totalDeposito = (float) $totalDeposito + (float) $te->total;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 1 || 
                    $te->credit_note->type_credit_note_id == 2 || $te->credit_note->type_credit_note_id == 6) {
                    $totalDeposito = (float) $totalDeposito + 0;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 4 || 
                    $te->credit_note->type_credit_note_id == 5 || $te->credit_note->type_credit_note_id == 7 ||
                    $te->credit_note->type_credit_note_id == 9) {
                    $totalDeposito = (float) $totalDeposito + (float) $te->credit_note->total;
                }
            }

            $quotationsEfectivo = Quotation::where('headquarter_id', $this->headquarter)
                ->where('created_at', '>=', $cash->opening_hour)
                ->where('created_at', '<=', $cash->closing_hour)
                ->where(function($query) use ($cash, $typeCash) {
                    if ($typeCash == 0) {
                        $query->where('headquarter_id', $cash->headquarter_id);
                    } else {
                        $query->where('user_id', $cash->user_id);
                    }
                })
                ->where('status', 1)
                ->where('is_order_note', 1)
                ->where('condition', 'EFECTIVO')
                ->orderBy('created_at', 'asc')
                ->sum('total');

            $quotationsTarjetaCredito = Quotation::where('headquarter_id', $this->headquarter)
                ->where('created_at', '>=', $cash->opening_hour)
                ->where('created_at', '<=', $cash->closing_hour)
                ->where(function($query) use ($cash, $typeCash) {
                    if ($typeCash == 0) {
                        $query->where('headquarter_id', $cash->headquarter_id);
                    } else {
                        $query->where('user_id', $cash->user_id);
                    }
                })
                ->where('status', 1)
                ->where('is_order_note', 1)
                ->where('condition', 'TARJETA DE CREDITO')
                ->orderBy('created_at', 'asc')
                ->sum('total');

            $quotationsTarjetaDebito = Quotation::where('headquarter_id', $this->headquarter)
                ->where('created_at', '>=', $cash->opening_hour)
                ->where('created_at', '<=', $cash->closing_hour)
                ->where(function($query) use ($cash, $typeCash) {
                    if ($typeCash == 0) {
                        $query->where('headquarter_id', $cash->headquarter_id);
                    } else {
                        $query->where('user_id', $cash->user_id);
                    }
                })
                ->where('status', 1)
                ->where('is_order_note', 1)
                ->where('condition', 'TARJETA DE DEBITO')
                ->orderBy('created_at', 'asc')
                ->sum('total');

            $totalPaymentCredits = PaymentCredit::where('client_id', auth()->user()->headquarter->client_id)
                                                    ->whereDate('created_at', date('Y-m-d'))
                                                    ->whereBetween('created_at', [$cash->opening_hour, $cash->closing_hour])
                                                    ->sum('payment');

            $ouputs = CashMovements::where('cash_id', $cash->id)
                                    ->whereBetween('created_at', [$cash->opening_hour, $cash->closing_hour])
                                    ->where('movement', 'SALIDA')
                                    ->where('type', 2)
                                    ->select('amount')
                                    ->sum('amount');
            $entries = CashMovements::where('cash_id', $cash->id)
                                    ->whereBetween('created_at', [$cash->opening_hour, $cash->closing_hour])
                                    ->where('movement', 'INGRESO')
                                    ->select('amount')
                                    ->where('type', 2)
                                    ->sum('amount');

            $facturaStart = $facturas->first();
            $facturaEnd = $facturas->last();
            $boletaStart = $boletas->first();
            $boletaEnd = $boletas->last();
            $quotationsStart = $quotations->first();
            $quotationsEnd = $quotations->last();

            $facturas2 = Sale::with('credit_note')
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->whereNull('low_communication_id')
                                ->where('status', 1)
                                ->where('typevoucher_id', 1)
                                ->where('created_at', '>=', $cash->opening_hour)
                                ->where('created_at', '<=', $cash->closing_hour)
                                ->where(function($query) use ($cash, $typeCash) {
                                    if ($typeCash == 0) {
                                        $query->where('headquarter_id', $cash->headquarter_id);
                                    } else {
                                        $query->where('user_id', $cash->user_id);
                                    }
                                })
                                ->orderBy('created_at', 'asc')
                                ->get();

            $boletas2 = Sale::with('credit_note')
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->whereNull('low_communication_id')
                                ->where('status', 1)
                                ->where('typevoucher_id', 2)
                                ->where('created_at', '>=', $cash->opening_hour)
                                ->where('created_at', '<=', $cash->closing_hour)
                                ->where(function($query) use ($cash, $typeCash) {
                                    if ($typeCash == 0) {
                                        $query->where('headquarter_id', $cash->headquarter_id);
                                    } else {
                                        $query->where('user_id', $cash->user_id);
                                    }
                                })
                                ->orderBy('created_at', 'asc')
                                ->get();

            $invoicesCredit = Sale::with('credit_note')
                                    ->where('client_id', auth()->user()->headquarter->client_id)
                                    ->whereNull('low_communication_id')
                                    ->where('condition_payment', 'CREDITO')
                                    ->where('status', 1)
                                    ->where(function($query) use ($cash, $typeCash) {
                                        if ($typeCash == 0) {
                                            $query->where('headquarter_id', $cash->headquarter_id);
                                        } else {
                                            $query->where('user_id', $cash->user_id);
                                        }
                                    })
                                    ->where('created_at', '>=', $cash->opening_hour)
                                    ->where('created_at', '<=', $cash->closing_hour)
                                    ->get();

            $totalFactura = 0;
            foreach ($facturas2 as $te) {
                if ($te->credit_note == null) {
                    $totalFactura = (float) $totalFactura + (float) $te->total;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 1 || 
                    $te->credit_note->type_credit_note_id == 2 || $te->credit_note->type_credit_note_id == 6) {
                    $totalFactura = (float) $totalFactura + 0;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 4 || 
                $te->credit_note->type_credit_note_id == 5 || $te->credit_note->type_credit_note_id == 7 || $te->credit_note->type_credit_note_id == 9) {
                    $totalFactura = (float) $totalFactura + (float) $te->credit_note->total;
                }
            }
            $totalBoleta = 0;
            foreach ($boletas2 as $te) {
                if ($te->credit_note == null) {
                    $totalBoleta = (float) $totalBoleta + (float) $te->total;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 1 || 
                    $te->credit_note->type_credit_note_id == 2 || $te->credit_note->type_credit_note_id == 6) {
                    $totalBoleta = (float) $totalBoleta + 0;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 4 || 
                $te->credit_note->type_credit_note_id == 5 || $te->credit_note->type_credit_note_id == 7 || $te->credit_note->type_credit_note_id == 9) {
                    $totalBoleta = (float) $totalBoleta + (float) $te->credit_note->total;
                }
            }

            $totalCredit = 0;
            foreach ($invoicesCredit as $te) {
                if ($te->credit_note == null) {
                    $totalCredit = (float) $totalCredit + (float) $te->total;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 1 || 
                    $te->credit_note->type_credit_note_id == 2 || $te->credit_note->type_credit_note_id == 6) {
                    $totalCredit = (float) $totalCredit + 0;
                } else if ($te->credit_note != null && $te->credit_note->type_credit_note_id == 4 || 
                            $te->credit_note->type_credit_note_id == 5 || $te->credit_note->type_credit_note_id == 7 || 
                            $te->credit_note->type_credit_note_id == 9) {
                    $totalCredit = (float) $totalCredit + (float) $te->credit_note->total;
                }
            }

            $liquidation = new LiquidationCash;
            $liquidation->transaction = (int) $transaction;
            $liquidation->factura_start = $facturaStart == null ? '-' : "{$facturaStart->serialnumber}{$facturaStart->correlative}";
            $liquidation->factura_end = $facturaEnd == null ? '-' : "{$facturaEnd->serialnumber}{$facturaEnd->correlative}";
            $liquidation->boleta_start = $boletaStart == null ? '-' : "{$boletaStart->serialnumber}{$boletaStart->correlative}";
            $liquidation->boleta_end = $boletaEnd == null ? '-' : "{$boletaEnd->serialnumber}{$boletaEnd->correlative}";
//            $liquidation->quotation_start = $quotationsStart == null ? '-' : "{$quotationsStart->serial_number}{$quotationsStart->correlative}";
//            $liquidation->quotation_end = $quotationsEnd == null ? '-' : "{$quotationsEnd->serialnumber}{$quotationsEnd->correlative}";
            $liquidation->total_factura = $totalFactura;
            $liquidation->total_boleta = $totalBoleta;
//            $liquidation->total_quotation = $quotations->sum('total');
            $liquidation->efectivo = $totalEfectivo + $quotationsEfectivo;
            $liquidation->tarjeta_credito = $totalTarjetaCreditoGet->sum('condition_payment_amount') + $quotationsTarjetaCredito + $totalTarjetaCreditoGetOther->sum('other_condition_mount');
            $liquidation->tarjeta_debito = $totalTarjetaDebitoGet->sum('condition_payment_amount') + $quotationsTarjetaDebito + $totalTarjetaDebitoGetOther->sum('other_condition_mount');
            $liquidation->deposito_cuenta = $totalDepositoGet->sum('condition_payment_amount') + $totalDepositoGetOther->sum('other_condition_mount');
//            $liquidation->tarjeta_credito = $totalTarjetaCredito + $quotationsTarjetaCredito;
//            $liquidation->tarjeta_debito = $totalTarjetaDebito + $quotationsTarjetaDebito;
//            $liquidation->deposito_cuenta = $totalDeposito;
            $liquidation->opening_amount = $cash->opening_amount;
            $liquidation->paid_cash = $totalEfectivo;
            $liquidation->output = $ouputs;
            $liquidation->entries = $entries;
            $liquidation->total =  number_format(((float) $totalEfectivo + (float) $cash->opening_amount + (float) $entries + (float) $totalPaymentCredits) - (float) $ouputs, 2, '.', '');
            $liquidation->cash_id = $cash->id;
            $liquidation->user_id = $cash->user_id;
            $liquidation->payment_credits = $totalPaymentCredits;
            $liquidation->total_credits = $totalCredit;
            $liquidation->client_id = auth()->user()->headquarter->client_id;
            $liquidation->headquarter_id = auth()->user()->headquarter_id;
            $liquidation->save();

            DB::commit();
            
            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return $e->getMessage();
        }
    }

    public function storeMovement(Request $request)
    {
        DB::beginTransaction();
        try {
            $movement = new CashMovements;
            $movement->movement = $request->type_movement;
            $movement->amount = $request->movement_amount;
            $movement->observation = $request->movement_observation;
            $movement->cash_id = $request->ci;
            $movement->type = 2;
            $movement->user_id = auth()->user()->id;
            $movement->save();

            DB::commit();
            
            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function movementIndex()
    {
        $cashes = Cash::where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', $this->headquarter)->get();

        return view('commercial.cashes.movements', compact('cashes'));
    }

    public function movementGenerate(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $movements = CashMovements::with('cash:id,name')
                                    ->whereHas('cash', function($query) {
                                        $query->where('client_id', auth()->user()->headquarter->client_id)
                                                ->where('headquarter_id', $this->headquarter);
                                    })
                                    ->where(function ($query) use($request) {
                                        if($request->get('filter_cash') != ''){
                                            $query->where('cash_id', $request->get('filter_cash'));
                                        }
                                    })
                                    ->whereDate('created_at', '>=', $from)
                                    ->whereDate('created_at', '<=', $to)
                                    ->get();

        return response()->json($movements);
    }

    public function movementGenerateExcel(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $movements = CashMovements::with('cash:id,name')
                                    ->where(function ($query) use($request) {
                                        if($request->get('filter_cash') != ''){
                                            $query->where('cash_id', $request->get('filter_cash'));
                                        }
                                    })
                                    ->whereHas('cash', function($query) {
                                        $query->where('client_id', auth()->user()->headquarter->client_id);
                                    })
                                    ->whereDate('created_at', '>=',$from)
                                    ->whereDate('created_at', '<=',$to)
                                    ->get();
        
        return (new MovementCashExport($movements, $from, $to))->download('Movimientos de Caja['. $from . '-' . $to .'].xlsx');
    }

    public function movementGeneratePDF(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $movements = CashMovements::with('cash:id,name')
                                    ->where(function ($query) use($request) {
                                        if($request->get('filter_cash') != ''){
                                            $query->where('cash_id', $request->get('filter_cash'));
                                        }
                                    })
                                    ->whereHas('cash', function($query) {
                                        $query->where('client_id', auth()->user()->headquarter->client_id);
                                    })
                                    ->whereDate('created_at', '>=',$from)
                                    ->whereDate('created_at', '<=',$to)
                                    ->get();

        $clientInfo = auth()->user()->headquarter->client;

        $date = Carbon::now()->format('d-m-Y');

        $pdf = PDF::loadView('commercial.cashes.pdfMovements', compact('movements', 'clientInfo','date', 'from', 'to'))->setPaper('A4');
        return $pdf->stream('Movimientos de Caja ' . $from . '-' . $to . '.pdf');
    }
}
