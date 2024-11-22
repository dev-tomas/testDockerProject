<?php

namespace App\Exports;

use App\Exports\SaleInterfaceSheet;
use App\Exports\SalesInterfaceHeadSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SalesInterfaceBookExport implements WithMultipleSheets
{
    use Exportable;

    public $sales;
    public $movements;
    public $voucher, $ai, $ar, $creditnotes, $debitNotes, $ab, $type;

    public function __construct($sales, $movements, $voucher, $ai, $ar,$ab, $creditnotes, $debitNotes, $type)
    {
        $this->sales = $sales;
        $this->movements = $movements;
        $this->voucher = $voucher;
        $this->ai = $ai;
        $this->ar = $ar;
        $this->ab = $ab;
        $this->creditnotes = $creditnotes;
        $this->debitNotes = $debitNotes;
        $this->type = $type;
    }
    
    public function sheets(): array
    {
        $sheets = [
            new SalesInterfaceHeadSheet($this->sales, $this->movements, $this->voucher, $this->creditnotes, $this->debitNotes, $this->type),
            new SaleInterfaceSheet($this->sales, $this->movements, $this->voucher, $this->ai, $this->ar, $this->ab, $this->creditnotes, $this->type)
        ];

        return $sheets;
    }
}
