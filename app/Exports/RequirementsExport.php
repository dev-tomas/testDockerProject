<?php

namespace App\Exports;

use App\Requirement;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RequirementsExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('logistic.requirements.excel', [
            'requirements' => Requirement::where('headquarter_id',auth()->user()->headquarter_id)->get()
        ]);
    }
}
