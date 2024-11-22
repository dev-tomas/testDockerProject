<?php

namespace App\Http\Controllers;

use DB;
use App\UserIconDashboard;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __contruct()
    {
        $this->middleware(['auth', 'status.client']);
    }

    public function redirectLogin() {
        return redirect()->route('login');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $sicon = UserIconDashboard::where('user_id', auth()->user()->id)->get();

            if (!empty($sicon[0])) {
                foreach ($request->icon as $i) {
                    $dicon = UserIconDashboard::find($sicon->id);
                    $dicon->delete();
                }
            }
            
            for ($i=0; $i < count($request->icon); $i++) { 
                $nicon = new UserIconDashboard;
                $nicon->user_id = auth()->user()->id;
                $nicon->icon_dashboard_id = $request->icon[$i];
                $nicon->save();
                
            }
            
            DB::commit();

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->success($e->getMessage());
            return redirect()->back();
        }
    }
}
