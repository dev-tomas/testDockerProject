<?php

namespace App\Http\Controllers;

use App\BankMovement;
use App\Imports\BankMovementsBbvaImport;
use App\Imports\BankMovementsBcpImport;
use App\Imports\BankMovementsInterbankImport;
use App\PaymentCredit;
use App\PurchaseCreditPayment;
use App\Sale;
use App\Shopping;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class BankMovementsController extends Controller
{
    public function index()
    {
        return view('accountancy.bank-movements.index');
    }

    public function import(Request $request)
    {
        if ($request->bank == 1) {
            (new BankMovementsBcpImport)->import($request->file('file'));
        } else if($request->bank == 2) {
            (new BankMovementsInterbankImport)->import($request->file('file'));
        } else {
            (new BankMovementsBbvaImport)->import($request->file('file'), null, \Maatwebsite\Excel\Excel::CSV);
        }

        return redirect()->back()->with('info', 'Se importo correctamente');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($request->line); $i++) {
                $currentLine = $request->line[$i];
                $data = $request['data'][$currentLine];

                $movement = BankMovement::find($data['movement']);
                $movement->sale_id = $data['movement_sale'];
                $movement->shopping_id = $data['movement_shopping'];
                $movement->save();

                if ($data['payment_line'] == null && $data['movement_sale'] != null) {
                    $sale = Sale::find($data['movement_sale']);
                    if ($sale != null) {
                        $sale->bank_movement_id = $movement->id;
                        $sale->save();
                    }
                }

                if ($data['payment_line'] == null && $data['movement_shopping'] != null) {
                    $sale = Shopping::find($data['movement_shopping']);
                    if ($sale != null) {
                        $sale->bank_movement_id = $movement->id;
                        $sale->save();
                    }
                }

                if ($data['payment_line'] != null && $data['movement_sale'] != null) {
                    $payment = PaymentCredit::find($data['payment_line']);
                    if ($payment != null) {
                        $payment->bank_movement_id = $movement->id;
                        $payment->save();

                        $pendingPayments = PaymentCredit::where('credit_client_id', $payment->credit_client_id)
                            ->where('client_id', auth()->user()->headquarter->client_id)
                            ->where('payment_type', 'DEPOSITO EN CUENTA')
                            ->whereNull('bank_movement_id')
                            ->count();

                        if ($pendingPayments < 1) {
                            $sale = Sale::find($data['movement_sale']);
                            if ($sale != null) {
                                $sale->bank_movement_id = $movement->id;
                                $sale->save();
                            }
                        }
                    }
                }

                if ($data['payment_line'] != null && $data['movement_shopping'] != null) {
                    $payment = PurchaseCreditPayment::find($data['payment_line']);
                    if ($payment != null) {
                        $payment->bank_movement_id = $movement->id;
                        $payment->save();

                        $pendingPayments = PurchaseCreditPayment::where('purchase_credit_id', $payment->credit_client_id)
                            ->where('client_id', auth()->user()->headquarter->client_id)
                            ->where('payment_type', 'DEPOSITO EN CUENTA')
                            ->whereNull('bank_movement_id')
                            ->count();

                        if ($pendingPayments < 1) {
                            $sale = Shopping::find($data['movement_shopping']);
                            if ($sale != null) {
                                $sale->bank_movement_id = $movement->id;
                                $sale->save();
                            }
                        }
                    }
                }
            }

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(false);
        }
    }

    public function getData(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $movements = BankMovement::where('client_id', auth()->user()->headquarter->client_id)
                                ->whereBetween('date', [$from, $to])
                                ->where(function($query) use ($request) {
                                    if ($request->bank_filter != '') {
                                        $query->where('bank', $request->bank_filter);
                                    }
                                })
                                ->where(function($query) {
                                    $query->whereNull('sale_id')
                                        ->whereNull('shopping_id');
                                })
                                ->get();

        $shoppingsDepositoCuenta = Shopping::where('client_id', auth()->user()->headquarter->client_id)
                                ->where('status', '!=', 9)
                                ->where('payment_type', 'DEPOSITO EN CUENTA')
                                ->whereNull('bank_movement_id')
                                ->orderBy('date', 'desc')
                                ->get(['id', 'shopping_serie', 'shopping_correlative', 'payment_type']);

        $shoppingsCreditPaymentDepositoCuenta = Shopping::where('client_id', auth()->user()->headquarter->client_id)
                                            ->where('status', '!=', 9)
                                            ->where('payment_type', 'CREDITO')
                                            ->whereHas('credit', function($query) {
                                                $query->whereHas('payment', function ($q) {
                                                    $q->where('payment_type', 'DEPOSITO EN CUENTA')
                                                        ->whereNull('bank_movement_id');
                                                });
                                            })
                                            ->orderBy('date', 'desc')
                                            ->get(['id', 'shopping_serie', 'shopping_correlative', 'payment_type']);

        $salesDepositoCuenta = Sale::where('client_id', auth()->user()->headquarter->client_id)
                                    ->whereNull('low_communication_id')
                                    ->where('condition_payment', 'DEPOSITO EN CUENTA')
                                    ->whereNull('bank_movement_id')
                                    ->orderBy('date', 'desc')
                                    ->get(['id', 'serialnumber', 'correlative', 'condition_payment']);

        $salesCreditPaymentDepositoCuenta = Sale::where('client_id', auth()->user()->headquarter->client_id)
                                                    ->whereHas('credito', function($query) {
                                                        $query->whereHas('payments', function ($q) {
                                                           $q->where('payment_type', 'DEPOSITO EN CUENTA')
                                                               ->whereNull('bank_movement_id');
                                                        });
                                                    })
                                                    ->whereNull('low_communication_id')
                                                    ->where('condition_payment', 'CREDITO')
                                                    ->orderBy('date', 'desc')
                                                    ->get(['id', 'serialnumber', 'correlative', 'condition_payment']);

        $sales = $salesDepositoCuenta->merge($salesCreditPaymentDepositoCuenta);
        $shoppings = $shoppingsDepositoCuenta->merge($shoppingsCreditPaymentDepositoCuenta);

        return response()->json(['movements' => $movements, 'sales' => $sales, 'shoppings' => $shoppings]);
    }

    public function getPayments(Request $request)
    {
        $payments = PaymentCredit::with('bank:id,bank_name', 'cash:id,name', 'paymentMethod:id,name')
            ->where('credit_client_id', $request->credit_id)
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->where('payment_type', 'DEPOSITO EN CUENTA')
            ->get(['date', 'payment_type', 'payment', 'bank', 'operation_bank','bank_account_id', 'payment_method_id', 'id', 'bank_movement_id']);

        return response()->json($payments);
    }

    public function getPaymentsShopping(Request $request)
    {
        $payments = PurchaseCreditPayment::with('bank:id,bank_name', 'cash:id,name', 'paymentMethod:id,name')
            ->where('purchase_credit_id', $request->credit_id)
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->where('payment_type', 'DEPOSITO EN CUENTA')
            ->get(['date', 'payment_type', 'payment', 'bank', 'operation_bank','bank_account_id', 'payment_method_id', 'id', 'bank_movement_id']);

        return response()->json($payments);
    }
}
