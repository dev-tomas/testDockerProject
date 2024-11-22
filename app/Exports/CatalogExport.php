<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CatalogExport implements WithMultipleSheets
{
    use Exportable;

    public $data;
    public $client;

    public function __construct($data, $client)
    {
        $this->data = $data;
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new CatalogoFrontPageExport($this->data, $this->client);
        foreach ($this->data as $sheet) {
            $sheets[] = new CatalogSheetExport($sheet, $this->client);
        }

        return $sheets;
    }
}
