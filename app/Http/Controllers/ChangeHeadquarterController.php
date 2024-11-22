<?php

namespace App\Http\Controllers;

use App\HeadQuarter;
use Illuminate\Http\Request;

class ChangeHeadquarterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
    }

    public static function getHeadquarter()
    {
        $headquarters = HeadQuarter::where('client_id', auth()->user()->headquarter->client_id)->get(['id', 'description']);

        return $headquarters;
    }

    public function change(Request $request)
    {
        session()->forget('headlocal');
        $newLocal = $request->headquarter;
        session()->put('headlocal', $newLocal);
        return response()->json(true);
    }

    public static function currentLocal()
    {
        if (session()->has('headlocal')) {
            $id = session()->get('headlocal');
        } else {
            $id = auth()->user()->headquarter_id;
        }
        $headquarter = HeadQuarter::select('description')->find($id);

        return $headquarter;
    }
}
