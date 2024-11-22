<?php

namespace App\Exports;

use App\Sale;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoicesExport implements FromView
{
    protected $since;
    protected $until;
    protected $type_voucher;

    public function __construct($since, $until, $type_voucher)
    {
        $this->since        =   $since;
        $this->until        =   $until;
        $this->type_voucher =   $type_voucher;
    }

    public function view(): View
    {
        $sales = Sale::with('type_voucher', 'customer', 'customer.document_type', 'detail', 'credit_note.type_voucher','debit_note.type_voucher', 'sunat_code')
            ->where(function ($query) {
                if($this->since != '0' && $this->until != '0'){
                    $query->whereBetween('sales.date',  [date('Y-m-d', strtotime($this->since)), date('Y-m-d', strtotime($this->until))]);
                }
            })->get();


        return view('export.invoices', [
            'sales' => $sales
        ]);
    }
}
