<?php

namespace App\Http\Controllers;

use App\Kardex;
use App\Mail\RegisterMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TestsController extends Controller
{
    public function fixKardexCost()
    {
        DB::beginTransaction();
        try {
            $kardexes = Kardex::where('cost', '>', 0)->get();

            foreach ($kardexes as $kardex) {
                $k = Kardex::find($kardex->id);
                $k->cost = $kardex->cost / 1.18;
                $k->save();
            }

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(false);
        }
    }
}
