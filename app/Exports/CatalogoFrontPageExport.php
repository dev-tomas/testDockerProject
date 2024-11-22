<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CatalogoFrontPageExport implements FromView, WithTitle, WithEvents, WithStyles
{
    public $data;
    public $client;
    public function __construct($data, $client)
    {
        $this->data = $data;
        $this->client = $client;
    }

    public function view(): View
    {
        $view = view('warehouse.product.catalog-front', ['data' => $this->data, 'client' => $this->client]);

        return $view;
    }

    public function title(): string
    {
        return 'PRODUCTOS';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {

            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $categoryStyle = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '002060',
                ],
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
                'underline' => false,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->setShowGridlines(false);
        $totalRows = $sheet->getHighestRow();
        $rangeCategories = "C5:C{$totalRows}";
        for ($row = 5; $row <= $totalRows; $row++) {
            $cellValue = $sheet->getCell('C'.$row)->getValue();
            if (!empty($cellValue)) {
                $sheet->getRowDimension($row)->setRowHeight(25);
                $sheet->getStyle('C'.$row)->applyFromArray($categoryStyle);
            }
            $cellValue = $sheet->getCell('E'.$row)->getValue();
            if (!empty($cellValue)) {
                $sheet->getStyle('E'.$row)->applyFromArray($categoryStyle);
            }
        }
    }
}
