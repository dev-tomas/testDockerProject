<?php

namespace App\Exports;

use App\Exports\FinancesHeadSheet;
use App\Exports\FinancesDetailSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class FinancesBookExport implements WithMultipleSheets
{
    use Exportable;

    public $sales;
    public $movements;
    public $voucher;
    public $sd;
    public $notes;

    public function __construct($sales, $sd, $movements, $voucher, $notes)
    {
        $this->sales = $sales;
        $this->movements = $movements;
        $this->voucher = $voucher;
        $this->sd = $sd;
        $this->notes = $notes;
    }
    
    public function sheets(): array
    {
        $sheets = [
            new FinancesHeadSheet($this->sales, $this->sd, $this->movements, $this->voucher, $this->notes),
            new FinancesDetailSheet($this->sales, $this->sd, $this->movements, $this->voucher, $this->notes)
        ];

        return $sheets;
    }
}
