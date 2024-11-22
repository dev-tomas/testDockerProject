<?php

namespace App\Http\Controllers;

use App\CreditClient;
use PDF;
use App\Provider;
use App\Shopping;
use Carbon\Carbon;
use App\CashMovements;
use App\PurchaseCredit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\PurchaseCreditPayment;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseCreditExport;

class CreditsProviderController extends Controller
{
    public function __contruct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
    }

    public function index()
    {
        $providers = Provider::where('client_id', auth()->user()->headquarter->client_id)->get();

        return view('finances.providercredit.index', compact('providers'));
    }

    public function dt_creditsProviders(Request $request)
    {
        $credits = PurchaseCredit::with('shopping', 'provider', 'shopping.coin')
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->where(function ($query) use($request) {
                                    if($request->get('denomination') != ''){
                                        $query->where('provider_id', $request->get('denomination'));
                                    }
                                    if($request->get('status') != ''){
                                        if ($request->get('status') == 2) {
                                            $query->where('expiration', '<=', date('Y-m-d') );
                                        } else {
                                            $query->where('status', $request->get('status'));
                                        }
                                    }
                                    if($request->get('dateOne') != ''){
                                        $query->whereBetween('date',  [$request->get('dateOne'), $request->get('dateTwo')]);
                                    }
                                })
                                ->get();

        return datatables()->of($credits)->toJson();
    }

    public function getPayment(Request $request)
    {
        $payments = PurchaseCreditPayment::with('bank:id,bank_name', 'cash:id,name', 'paymentMethod:id,name')
                                ->where('purchase_credit_id', $request->credit_id)
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->get(['id','date', 'payment_type', 'payment', 'bank', 'operation_bank', 'cash_id', 'payment_method_id', 'bank_account_id']);

        return response()->json($payments);
    }

    public function getCredit(Request $request)
    {
        $credit = PurchaseCredit::where('purchase_id', $request->shopping)
                                        ->where('client_id', auth()->user()->headquarter->client_id)
                                        ->first();

        return response()->json($credit);
    }

    public function storePayment(Request $request)
    {
        DB::beginTransaction();
        try{
            $payment = new PurchaseCreditPayment;
            $payment->date = date('Y-m-d', strtotime($request->payment_date));
            $payment->bank = $request->bank;
            $payment->operation_bank = $request->operation_bank;
            $payment->payment_type = $request->payment_type;
            $payment->payment = $request->payment_mont;
            $payment->purchase_credit_id = $request->credit_id;
            $payment->client_id = auth()->user()->headquarter->client_id;
            if ($request->payment_type == 'EFECTIVO') {
                $payment->cash_id = $request->cash;
            }
            if ($request->payment_type == 'DEPOSITO EN CUENTA') {
                $payment->bank_account_id = $request->bank;
            }
            if ($request->payment_type == 'TARJETA DE CREDITO' || $request->payment_type == 'TARJETA DE DEBITO') {
                $payment->payment_method_id = $request->mp;
            }
            $payment->save();

            $credit = PurchaseCredit::where('id', $request->credit_id)
                                    ->where('client_id', auth()->user()->headquarter->client_id)
                                    ->first();
            $oldDebt = $credit->debt;
            $newDebt = (float) $oldDebt - (float) $request->payment_mont;
            $credit->debt = $newDebt;
            if ($newDebt <= 0.00) {
                $credit->status = 1;
            }
            $credit->save();

            if ($request->payment_type == 'EFECTIVO') {
                $shopping = Shopping::find($credit->purchase_id);

                $movement = new CashMovements;
                $movement->movement = 'SALIDA';
                $movement->amount = $request->payment_mont;
                $movement->observation = "{$shopping->shopping_serie}-{$shopping->shopping_correlative}";
                $movement->cash_id = $request->cash;
                $movement->user_id = auth()->user()->id;
                $movement->save();

                if ($credit->status == 1) {
                    $shopping->paidout = 1;
                    $shopping->save();
                }
            }

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {           
            DB::rollBack();

            return response()->json(false);
        }
    }

    public function exporCredit(Request $request)
    {
        $now = new \DateTime();
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->date, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->date, '- '))->format('Y-m-d');
        $client = $request->get('customer');
        $status = $request->get('status');

        return Excel::download(new PurchaseCreditExport($to, $from, $client, $status), 'Pagos a Proveedores ['.$now->format('d-m-y').'].xlsx');
    }

    public function exporCreditPdf(Request $request)
    {
        $now = new \DateTime();
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->date, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->date, '- '))->format('Y-m-d');

        $credits = PurchaseCredit::with('shopping', 'provider', 'payment')
                                        ->where('client_id', auth()->user()->headquarter->client_id)
                                        ->where(function ($query) use($request, $from, $to) {
                                            if($request->get('customer') != ''){
                                                $query->where('provider_id', $request->get('customer'));
                                            }
                                            if($request->get('status') != ''){
                                                if ($request->get('status') == 2) {
                                                    $query->where('expiration', '<=', date('Y-m-d') );
                                                } else {
                                                    $query->where('status', $request->get('status'));
                                                }
                                            }
                                            if($request->get('date') != ''){
                                                $query->whereBetween('date',  [$from, $to]);
                                            }
                                        })
                                        ->get();
        $clientInfo = auth()->user()->headquarter->client;

        $pdf = PDF::loadView('finances.providercredit.pdf', compact('credits', 'clientInfo'))->setPaper('A4');
        return $pdf->stream('Pagos a Proveedores ['.$now->format('d-m-y').'].pdf');
    }
}
