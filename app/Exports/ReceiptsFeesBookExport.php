<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReceiptsFeesBookExport implements WithMultipleSheets
{
    use Exportable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [
            new ReceiptsFeesHeadSheet($this->data['head']),
            new ReceiptsFeesBodySheet($this->data['body'])
        ];

        return $sheets;
    }
}
