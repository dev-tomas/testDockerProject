<?php

namespace App\Http\Controllers\Manage;

use App\ApiRequest;
use App\ClientToken;
use App\Sale;
use App\Client;
use App\Summary;
use App\DebitNote;
use App\CreditNote;
use App\ReferenceGuide;
use App\LowCommunication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\SunatController;
use Illuminate\Support\Str;

class MonitorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->ajax = new AjaxController();
    }

    public function index()
    {
        $companies = Client::where('status', 1)->where('production', 1)->get(['id', 'document', 'trade_name']);
        return view('manage.monitor.monitor', compact('companies'));
    }

    public function cpes()
    {
        $companies = Client::where('status', 1)->where('production', 1)->get(['id', 'document', 'trade_name']);
        return view('manage.cpes.cpes', compact('companies'));
    }

    public function cpedt(Request $request)
    {
        $sales = Sale::with('customer', 'client', 'type_voucher', 'sunat_code')
                    ->whereHas('client', function($query) {
                        $query->where('production', 1);
                    })
                    ->where(function($query) use ($request) {
                        if ($request->companies != '') {
                            $query->where('sales.client_id', $request->companies);
                        }

                        if($request->get('serial') != '') {
                            $query->where('correlative', 'like', '%' . $request->get('serial') . '%');
                        }

                        if($request->get('date') != '') {
                            $query->where('date', date('Y-m-d', strtotime($request->get('date'))));
                        }

                        if ($request->onlylows == 1) {
                            $query->whereNotNull('sales.low_communication_id');
                        } else {
                            if ($request->cpe != '') {
                                if ($request->cpe == 5) {
                                    $query->where(function ($q) use ($request) {
                                        $q->whereNull('sales.cpe_status')
                                                ->orWhere('sales.cpe_status', '-');
                                    });
                                } else {
                                    $query->where('sales.cpe_status', $request->cpe);
                                }
                            }

                            if ($request->invoice != '') {
                                if ($request->invoice == 2) {
                                    $query->whereNotNull('sales.invoice_status')
                                            ->where('sales.invoice_status', '!=', 9);
                                } else {
                                    $query->where('sales.invoice_status', $request->invoice);
                                }
                            }
                        }
                    })
                    ->get();

        return datatables()->of($sales)->toJson();
    }

    public function dt(Request $request)
    {
        $invoices = array();
        $cont = 0;

        $sales = collect([]);
        if($request->get('document_type') == null || $request->get('document_type') == 1 || $request->get('document_type') == 2) {
            $sales = Sale::with('coin', 'customer:id,description,document', 'type_voucher','sunat_code', 'client:id,document,trade_name')
                                ->whereHas('client', function($query) {
//                                    $query->where('production', 1);
                                })
                                ->whereBetween('date', [$request->dateOne, $request->dateTwo])
                                ->where('status', '!=', 2)
                                ->where(function($query) use ($request) {
                                    $query->where('response_sunat', '>', 1);
                                    $query->orWhereNull('response_sunat');
                                })
                                ->where(function($query) use ($request) {
                                    if($request->get('company') != null) {
                                        $query->where('client_id', $request->get('company'));
                                    }
                                })
                                ->get([
                                    'id',
                                    'total',
                                    'issue as date',
                                    'customer_id',
                                    'typevoucher_id',
                                    'correlative',
                                    'serialnumber',
                                    'response_sunat',
                                    'status_sunat',
                                    'coin_id',
                                    'client_id',
                                    'cpe_status',
                                    'invoice_status',
                                ]);
        }

        $creditNotes = collect([]);
        if($request->get('document_type') == '' || $request->get('document_type') == 3 || $request->get('document_type') == 4) {
            $creditNotes = CreditNote::with('customer:id,description,document', 'type_voucher','sunat_code', 'client:id,document,trade_name')
                            ->whereBetween('date_issue', [$request->get('dateOne'), $request->get('dateTwo')])
                            ->whereHas('client', function($query) {
                                $query->where('production', 1);
                            })
                            ->where(function($query) use ($request) {
                                $query->where('response_sunat', '>', 1);
                                $query->orWhereNull('response_sunat');
                            })
                            ->where(function($query) use ($request) {
                                if($request->get('document_type') != '') {
                                    $query->where('typevoucher_id', $request->get('document_type'));
                                }
                                if($request->get('company') != null) {
                                    $query->where('client_id', $request->get('company'));
                                }
                            })
                            ->get([
                                'id',
                                'total',
                                'date_issue as date',
                                'customer_id',
                                'typevoucher_id',
                                'correlative',
                                'serial_number as serialnumber',
                                'response_sunat',
                                'status_sunat',
                                'client_id',
                            ]);
        }

        $debitNotes = collect([]);
        if($request->get('document_type') == '' || $request->get('document_type') == 5 || $request->get('document_type') == 6) {
            $debitNotes = DebitNote::with('coin', 'customer:id,description,document', 'type_voucher','sunat_code', 'client:id,document,trade_name')
                            ->whereBetween('date_issue', [$request->get('dateOne'), $request->get('dateTwo')])
                            ->whereHas('client', function($query) {
                                $query->where('production', 1);
                            })
                            ->where(function($query) use ($request) {
                                $query->where('response_sunat', '>', 1);
                                $query->orWhereNull('response_sunat');
                            })
                            ->where(function($query) use ($request) {
                                if($request->get('document_type') != '') {
                                    $query->where('typevoucher_id', $request->get('document_type'));
                                }
                                if($request->get('company') != null) {
                                    $query->where('client_id', $request->get('company'));
                                }
                            })
                            ->get([
                                'id',
                                'total',
                                'date_issue as date',
                                'customer_id',
                                'typevoucher_id',
                                'correlative',
                                'serial_number as serialnumber',
                                'response_sunat',
                                'status_sunat',
                                'client_id',
                            ]);
        }

        $summaries = collect([]);

        if($request->get('document_type') == '' || $request->get('document_type') == 22) {
            $summaries = Summary::with('sunat_code', 'client:id,document,trade_name')
                                ->whereBetween('date_issues', [$request->get('dateOne'), $request->get('dateTwo')])
                                ->whereHas('client', function($query) {
                                    $query->where('production', 1);
                                })
                                ->where(function($query) use ($request) {
                                    $query->where('response_sunat', '>', 1);
                                    $query->orWhereNull('response_sunat');
                                })
                                ->where(function($query) use ($request) {
                                    if($request->get('company') != null) {
                                        $query->where('client_id', $request->get('company'));
                                    }
                                })
                                ->get([
                                    'id',
                                    'date_issues as date',
                                    'correlative',
                                    'response_sunat',
                                    'status_sunat',
                                    'client_id',
                                ]);
        }

        $lows = collect([]);
        if($request->get('document_type') == '' || $request->get('document_type') == 19) {
            $lows = LowCommunication::with('sunat', 'client:id,document,trade_name')
                                        ->whereBetween('generation_date', [$request->get('dateOne'), $request->get('dateTwo')])
                                        ->whereHas('client', function($query) {
                                            $query->where('production', 1);
                                        })
                                        ->where(function($query) use ($request) {
                                            $query->where('sunat_code_id', '>', 1);
                                            $query->orWhereNull('sunat_code_id');
                                        })
                                        ->where(function($query) use ($request) {
                                            if($request->get('company') != null) {
                                                $query->where('client_id', $request->get('company'));
                                            }
                                        })
                                        ->get([
                                            'id',
                                            'generation_date as date',
                                            'correlative',
                                            'sunat_code_id as response_sunat',
                                            'status_sunat',
                                            'client_id',
                                        ]);
        }

        $referenceGuides = collect([]);
        if($request->get('document_type') == '' || $request->get('document_type') == 7) {
            $referenceGuides = ReferenceGuide::with('customer:id,description,document', 'type_voucher','sunat_code', 'client:id,document,trade_name')
                                            ->whereBetween('date', [$request->get('dateOne'), $request->get('dateTwo')])
                                            ->whereHas('client', function($query) {
                                                $query->where('production', 1);
                                            })
                                            ->where(function($query) use ($request) {
                                                $query->where('response_sunat', '>', 1);

                                                $query->orWhereNull('response_sunat');
                                            })
                                            ->where(function($query) use ($request) {
                                                if($request->get('company') != null) {
                                                    $query->where('client_id', $request->get('company'));
                                                }
                                            })
                                            ->get([
                                                'id',
                                                'date as date',
                                                'customer_id',
                                                'typevoucher_id',
                                                'correlative',
                                                'serialnumber',
                                                'response_sunat',
                                                'status_sunat',
                                                'client_id',
                                                'receiver_document',
                                                'receiver',
                                            ]);
        }


        foreach ($sales as $sale) {
            if($request->get('document_type') != null && $request->get('document_type') == $sale->typevoucher_id) {
                $invoices[$cont]['id'] = $sale->id;
                $invoices[$cont]['total'] = $sale->total;
                $invoices[$cont]['date'] = $sale->date;
                $invoices[$cont]['customer'] = "{$sale->customer->document} - {$sale->customer->description}";
                $invoices[$cont]['typevoucher'] = $sale->typevoucher_id;
                $invoices[$cont]['voucher'] = $sale->type_voucher->description;
                $invoices[$cont]['correlative'] = $sale->correlative;
                $invoices[$cont]['serialnumber'] = $sale->serialnumber;
                $invoices[$cont]['response_sunat'] = $sale->response_sunat;
                $invoices[$cont]['status_sunat'] = $sale->status_sunat;
                $invoices[$cont]['coin'] = $sale->coin->symbol;
                $invoices[$cont]['client'] = "{$sale->client->document} - {$sale->client->trade_name}";
                $invoices[$cont]['client_id'] = $sale->client_id;
                $invoices[$cont]['cpe_status'] = $sale->cpe_status;
                $invoices[$cont]['invoice_status'] = $sale->invoice_status;
                if ($sale->sunat_code == null) {
                    $invoices[$cont]['sunat_code'] = $sale->sunat_code;
                } else {
                    $invoices[$cont]['sunat_code']['code'] = $sale->sunat_code->code;
                    $invoices[$cont]['sunat_code']['description'] = $sale->sunat_code->description;
                    $invoices[$cont]['sunat_code']['detail'] = $sale->sunat_code->detail;
                    $invoices[$cont]['sunat_code']['what_to_do'] = $sale->sunat_code->what_to_do;
                }

            } else if ($request->get('document_type') == null) {
                $invoices[$cont]['id'] = $sale->id;
                $invoices[$cont]['total'] = $sale->total;
                $invoices[$cont]['date'] = $sale->date;
                $invoices[$cont]['customer'] = "{$sale->customer->document} - {$sale->customer->description}";
                $invoices[$cont]['client_id'] = $sale->client_id;
                $invoices[$cont]['typevoucher'] = $sale->typevoucher_id;
                $invoices[$cont]['voucher'] = $sale->type_voucher->description;
                $invoices[$cont]['correlative'] = $sale->correlative;
                $invoices[$cont]['serialnumber'] = $sale->serialnumber;
                $invoices[$cont]['response_sunat'] = $sale->response_sunat;
                $invoices[$cont]['status_sunat'] = $sale->status_sunat;
                $invoices[$cont]['coin'] = $sale->coin->symbol;
                $invoices[$cont]['cpe_status'] = $sale->cpe_status;
                $invoices[$cont]['invoice_status'] = $sale->invoice_status;
                $invoices[$cont]['client'] = "{$sale->client->document} - {$sale->client->trade_name}";
                $invoices[$cont]['cpe_status'] = '-';
                $invoices[$cont]['invoice_status'] = '-';
                if ($sale->sunat_code == null) {
                    $invoices[$cont]['sunat_code'] = $sale->sunat_code;
                } else {
                    $invoices[$cont]['sunat_code']['code'] = $sale->sunat_code->code;
                    $invoices[$cont]['sunat_code']['description'] = $sale->sunat_code->description;
                    $invoices[$cont]['sunat_code']['detail'] = $sale->sunat_code->detail;
                    $invoices[$cont]['sunat_code']['what_to_do'] = $sale->sunat_code->what_to_do;
                }
            }

            $cont++;
        }
        foreach ($creditNotes as $sale) {
            $invoices[$cont]['id'] = $sale->id;
            $invoices[$cont]['total'] = $sale->total;
            $invoices[$cont]['date'] = $sale->date;
            $invoices[$cont]['customer'] = "{$sale->customer->document} - {$sale->customer->description}";
            $invoices[$cont]['client_id'] = $sale->client_id;
            $invoices[$cont]['typevoucher'] = $sale->typevoucher_id;
            $invoices[$cont]['voucher'] = $sale->type_voucher->description;
            $invoices[$cont]['correlative'] = $sale->correlative;
            $invoices[$cont]['serialnumber'] = $sale->serialnumber;
            $invoices[$cont]['response_sunat'] = $sale->response_sunat;
            $invoices[$cont]['status_sunat'] = $sale->status_sunat;
            $invoices[$cont]['coin'] = 'S/';
            $invoices[$cont]['client'] = "{$sale->client->document} - {$sale->client->trade_name}";
            $invoices[$cont]['cpe_status'] = '-';
            $invoices[$cont]['invoice_status'] = '-';
            if ($sale->sunat_code == null) {
                $invoices[$cont]['sunat_code'] = $sale->sunat_code;
            } else {
                $invoices[$cont]['sunat_code']['code'] = $sale->sunat_code->code;
                $invoices[$cont]['sunat_code']['description'] = $sale->sunat_code->description;
                $invoices[$cont]['sunat_code']['detail'] = $sale->sunat_code->detail;
                $invoices[$cont]['sunat_code']['what_to_do'] = $sale->sunat_code->what_to_do;
            }

            $cont++;
        }
        foreach ($debitNotes as $sale) {
            $invoices[$cont]['id'] = $sale->id;
            $invoices[$cont]['total'] = $sale->total;
            $invoices[$cont]['date'] = $sale->date;
            $invoices[$cont]['customer'] = "{$sale->customer->document} - {$sale->customer->description}";
            $invoices[$cont]['client_id'] = $sale->client_id;
            $invoices[$cont]['typevoucher'] = $sale->typevoucher_id;
            $invoices[$cont]['voucher'] = $sale->type_voucher->description;
            $invoices[$cont]['correlative'] = $sale->correlative;
            $invoices[$cont]['serialnumber'] = $sale->serialnumber;
            $invoices[$cont]['response_sunat'] = $sale->response_sunat;
            $invoices[$cont]['status_sunat'] = $sale->status_sunat;
            $invoices[$cont]['coin'] = $sale->coin->symbol;
            $invoices[$cont]['client'] = "{$sale->client->document} - {$sale->client->trade_name}";
            $invoices[$cont]['cpe_status'] = '-';
            $invoices[$cont]['invoice_status'] = '-';
            if ($sale->sunat_code == null) {
                $invoices[$cont]['sunat_code'] = $sale->sunat_code;
            } else {
                $invoices[$cont]['sunat_code']['code'] = $sale->sunat_code->code;
                $invoices[$cont]['sunat_code']['description'] = $sale->sunat_code->description;
                $invoices[$cont]['sunat_code']['detail'] = $sale->sunat_code->detail;
                $invoices[$cont]['sunat_code']['what_to_do'] = $sale->sunat_code->what_to_do;
            }

            $cont++;
        }
        foreach ($summaries as $sale) {
            $invoices[$cont]['id'] = $sale->id;
            $invoices[$cont]['total'] = '-';
            $invoices[$cont]['date'] = $sale->date;
            $invoices[$cont]['customer'] = '-';
            $invoices[$cont]['typevoucher'] = '22';
            $invoices[$cont]['voucher'] = 'RESUMEN DIARIO DE BOLETAS';
            $invoices[$cont]['correlative'] = $sale->correlative;
            $invoices[$cont]['serialnumber'] = 'RD';
            $invoices[$cont]['response_sunat'] = $sale->response_sunat;
            $invoices[$cont]['status_sunat'] = $sale->status_sunat;
            $invoices[$cont]['coin'] = '-';
            $invoices[$cont]['client'] = "{$sale->client->document} - {$sale->client->trade_name}";
            $invoices[$cont]['client_id'] = $sale->client_id;
            $invoices[$cont]['cpe_status'] = '-';
            $invoices[$cont]['invoice_status'] = '-';
            if ($sale->sunat_code == null) {
                $invoices[$cont]['sunat_code'] = $sale->sunat_code;
            } else {
                $invoices[$cont]['sunat_code']['code'] = $sale->sunat_code->code;
                $invoices[$cont]['sunat_code']['description'] = $sale->sunat_code->description;
                $invoices[$cont]['sunat_code']['detail'] = $sale->sunat_code->detail;
                $invoices[$cont]['sunat_code']['what_to_do'] = $sale->sunat_code->what_to_do;
            }

            $cont++;
        }
        foreach ($lows as $sale) {
            $invoices[$cont]['id'] = $sale->id;
            $invoices[$cont]['total'] = '-';
            $invoices[$cont]['date'] = $sale->date;
            $invoices[$cont]['customer'] = '-';
            $invoices[$cont]['client_id'] = null;
            $invoices[$cont]['typevoucher'] = '19';
            $invoices[$cont]['voucher'] = 'COMUNICACIÓN DE BAJA';
            $invoices[$cont]['correlative'] = $sale->correlative;
            $invoices[$cont]['serialnumber'] = 'L';
            $invoices[$cont]['response_sunat'] = $sale->response_sunat;
            $invoices[$cont]['status_sunat'] = $sale->status_sunat;
            $invoices[$cont]['coin'] = '-';
            $invoices[$cont]['client'] = "{$sale->client->document} - {$sale->client->trade_name}";
            $invoices[$cont]['cpe_status'] = '-';
            $invoices[$cont]['invoice_status'] = '-';
            if ($sale->sunat == null) {
                $invoices[$cont]['sunat_code'] = $sale->sunat;
            } else {
                $invoices[$cont]['sunat_code']['code'] = $sale->sunat->code;
                $invoices[$cont]['sunat_code']['description'] = $sale->sunat->description;
                $invoices[$cont]['sunat_code']['detail'] = $sale->sunat->detail;
                $invoices[$cont]['sunat_code']['what_to_do'] = $sale->sunat->what_to_do;
            }

            $cont++;
        }
        foreach ($referenceGuides as $sale) {
            $invoices[$cont]['id'] = $sale->id;
            $invoices[$cont]['total'] = '';
            $invoices[$cont]['date'] = $sale->date;
            $invoices[$cont]['customer'] = "{$sale->receiver_document} - {$sale->receiver}";
            $invoices[$cont]['client_id'] = $sale->client_id;
            $invoices[$cont]['typevoucher'] = $sale->typevoucher_id;
            $invoices[$cont]['voucher'] = $sale->type_voucher->description;
            $invoices[$cont]['correlative'] = $sale->correlative;
            $invoices[$cont]['serialnumber'] = $sale->serialnumber;
            $invoices[$cont]['response_sunat'] = $sale->response_sunat;
            $invoices[$cont]['status_sunat'] = $sale->status_sunat;
            $invoices[$cont]['coin'] = '-';
            $invoices[$cont]['client'] = "{$sale->client->document} - {$sale->client->trade_name}";
            $invoices[$cont]['cpe_status'] = '-';
            $invoices[$cont]['invoice_status'] = '-';
            if ($sale->sunat_code == null) {
                $invoices[$cont]['sunat_code'] = $sale->sunat_code;
            } else {
                $invoices[$cont]['sunat_code']['code'] = $sale->sunat_code->code;
                $invoices[$cont]['sunat_code']['description'] = $sale->sunat_code->description;
                $invoices[$cont]['sunat_code']['detail'] = $sale->sunat_code->detail;
                $invoices[$cont]['sunat_code']['what_to_do'] = $sale->sunat_code->what_to_do;
            }

            $cont++;
        }

        return datatables()->of($invoices)->toJson();
    }

    public function sendPendingInvoice($date)
    {
        $sales = Sale::where('issue', $date)->whereNull('response_sunat')->get();
        $salesResponse = [];
        $cont = 0;

        foreach ($sales as $sale) {
            $response = (new SunatController)->sendSunat($sale->id, 5, $sale->client_id);

            $salesResponse[$cont]['id'] = $sale->id;
            $salesResponse[$cont]['document'] = "{$sale->serialnumber} - {$sale->correlative}";
            $salesResponse[$cont]['client'] = $sale->client_id;
            $salesResponse[$cont]['response'] = $response;

            $cont++;
        }

        return response()->json($salesResponse);
    }

    public function api()
    {
        return view('manage.api.index');
    }

    public function apiDt(Request $request)
    {
        $desde = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $hasta = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $data = ApiRequest::whereBetween('created_at', [$desde, $hasta])
                        ->get(['host', 'created_at', 'payload', 'token', 'headers']);

        return datatables()->of($data)->toJson();
    }

    public function indexTokens()
    {
        $companies = Client::where('status', 1)
                            ->get(['id', 'document', 'trade_name']);

        return view('manage.api.tokens', compact('companies'));
    }

    public function dtTokens(Request $request)
    {
        $tokens = ClientToken::with('client:id,document,trade_name')->get();

        return datatables()->of($tokens)->toJson();
    }

    public function tokenChangeStatus(Request $request)
    {
        $token = ClientToken::find($request->token);
        $token->status = $request->type == 1 ? 'active' : 'disabled';
        $token->save();

        return response()->json(true);
    }

    public function tokenDelete(Request $request)
    {
        $token = ClientToken::find($request->token);
        $token->delete();

        return response()->json(true);
    }
}
