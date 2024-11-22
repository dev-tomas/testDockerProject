<?php

namespace App\Http\Controllers;

use App\Sale;
use App\CreditClient;
use App\PaymentCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditController extends Controller
{
    public function __contruct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
    }

    public function getPayment(Request $request)
    {
        $payments = PaymentCredit::with('bank:id,bank_name', 'cash:id,name', 'paymentMethod:id,name')
                                ->where('credit_client_id', $request->credit_id)
                                ->where('client_id', auth()->user()->headquarter->client_id)
                                ->get(['date', 'payment_type', 'payment', 'bank', 'operation_bank','bank_account_id','cash_id', 'payment_method_id']);

        return response()->json($payments);
    }

    public function getCredit(Request $request)
    {
        $credit = CreditClient::where('sale_id', $request->sale_id)->where('client_id', auth()->user()->headquarter->client_id)->first();

        return response()->json($credit);
    }

    public function storePayment(Request $request)
    {
        DB::beginTransaction();
        try{
            $payment = new PaymentCredit;
            $payment->date = date('Y-m-d', strtotime($request->payment_date));
            $payment->bank = $request->bank;
            $payment->operation_bank = $request->operation_bank;
            $payment->payment_type = $request->payment_type;
            $payment->payment = $request->payment_mont;
            $payment->credit_client_id = $request->credit_id;
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

            $credit = CreditClient::where('id', $request->credit_id)->where('client_id', auth()->user()->headquarter->client_id)->first();
            $sale = $credit->sale_id;
            $oldDebt = $credit->debt;
            $customer = $credit->customer_id;
            $newDebt = (float) $oldDebt - $request->payment_mont;
            if ($newDebt <= 0.00) {
                $credit->debt = 0.00;
                $credit->status = 1;

                $sale = Sale::find($sale);
                $sale->paidout = 1;
                $sale->status_condition = 1;
                $sale->save();
            } else {
                $credit->debt = $newDebt;
                $sale = Sale::find($sale);
                $sale->paidout = 2;
                $sale->save();
            }
            $credit->save();

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            echo $e->getCode();
            $rpta = 'Ooops';
            DB::rollBack();
            return response()->json($e);
        }
    }
}
