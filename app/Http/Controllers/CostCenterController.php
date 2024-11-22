<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\CostsCenter;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
    }
    public function index()
    {
        return view('logistic.costcenter.index');
    }

    public function dt_costcenter()
    {
        return datatables()->of(
            Db::table('costs_center')
                ->where('client_id', '=', Auth::user()->headquarter->client_id)
                ->get([
                    'id',
                    'center',
                    'code'
                ])
        )->toJson();
    }

    public function saveCenter(Request $request)
    {
        if($request->post('center_id')) {
            $center = CostsCenter::find($request->post('center_id'));
        } else{
            $center = new CostsCenter;
        }

        $center->code = $request->code;
        $center->center = $request->post('description');
        $center->client_id = Auth::user()->headquarter->client_id;

        if($center->save()) {
            echo json_encode(true);
        } else {
            echo response()->json(false);
        }
    }

    public function getCenters(Request $request)
    {
        $center = CostsCenter::where('id',$request->get('center_id'))->where('client_id', Auth::user()->headquarter->client_id)->first();
        return response()->json($center);
    }

    public function getCenter(Request $request)
    {
        $center = CostsCenter::where('client_id', Auth::user()->headquarter->client_id)->get();
        return response()->json($center);
    }

    public function deleteCenters(Request $request)
    {
        $center = CostsCenter::find($request->post('center_id'));
        if ($center->delete()) {
            return response()->json(true);
        } else {
            return response()->json(-1);
        }
    }
}
