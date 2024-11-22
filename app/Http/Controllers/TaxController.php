<?php

namespace App\Http\Controllers;

use App\Taxe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }
    public function index()
    {
        return view('tax.index');
    }

    public function dt_taxes()
    {
        return datatables()->of(
            Taxe::where('client_id', '=', auth()->user()->headquarter->client_id)
                ->get([
                'id',
                'description',
                'value'
            ])
        )->toJson();
    }

    public function saveTax(Request $request)
    {
        if($request->post('tax_id')) {
            $tax = Taxe::find($request->post('tax_id'));
        } else{
            $tax = new Taxe;
        }

        $tax->base = $request->post('base');
        $tax->description = $request->post('description');
        $tax->value = $request->post('value');
        $tax->client_id = auth()->user()->headquarter->client_id;

        if($tax->save()) {
            echo json_encode(true);
        } else {
            echo response()->json(false);
        }
    }

    public function deleteTax(Request $request)
    {
        $tax = Taxe::find($request->get('tax_id'));
        $tax->delete();

        return response()->json($tax->delete());
    }

    public function getTax(Request $request)
    {
        $tax = Taxe::where('id',$request->get('tax_id'))->where('client_id', Auth::user()->headquarter->client_id)->first();
        return response()->json($tax);
    }
}
