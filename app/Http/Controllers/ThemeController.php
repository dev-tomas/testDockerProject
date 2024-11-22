<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserInfo;

class ThemeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
    }

    public function store(Request $request)
    {
        $info = UserInfo::where('user_id', auth()->user()->id)->first();
        if ($info == null) {
            $info = new UserInfo;
        }
        $info->theme = $request->theme;
        if ($request->theme == 'buildings-theme.jpg') {
            $info->type_theme = 1;
        } else {
            $info->type_theme = 0;
        }
        $info->save();

        return response()->json(true);
    }
}
