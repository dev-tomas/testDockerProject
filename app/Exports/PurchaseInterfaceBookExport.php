<?php

namespace App\Exports;

use App\Exports\PurchaseInterfaceSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Exports\PurchaseInterfaceHeadSheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PurchaseInterfaceBookExport implements WithMultipleSheets
{
    use Exportable;

    public $book;
    public $type;

    public function __construct($book, $type)
    {
        $this->book = $book;
        $this->type = $type;
    }
    
    public function sheets(): array
    {
        $sheets = [
            new PurchaseInterfaceHeadSheet($this->book['head'], $this->type),
            new PurchaseInterfaceSheet($this->book['body'], $this->type)
        ];

        return $sheets;
    }
}
