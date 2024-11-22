<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProviderExpensesBookExport implements WithMultipleSheets
{
    use Exportable;

    public $sales;
    public $movements;
    public $voucher;
    public $sd;
    
    public function __construct($sales, $sd, $movements, $voucher)
    {
        $this->sales = $sales;
        $this->movements = $movements;
        $this->voucher = $voucher;
        $this->sd = $sd;
    }
    
    public function sheets(): array
    {
        $sheets = [
            new ProviderExpensesHeadSheet($this->sales, $this->sd, $this->movements, $this->voucher),
            new ProviderExpensesDetailSheet($this->sales, $this->sd, $this->movements, $this->voucher)
        ];

        return $sheets;
    }
}
