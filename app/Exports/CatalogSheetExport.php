<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CatalogSheetExport implements FromView,WithHeadings, ShouldAutoSize, WithEvents, WithTitle, WithStyles
{
    public $data;
    public $client;

    public function __construct($data, $client)
    {
        $this->data = $data;
        $this->client = $client;
    }

    public function title(): string
    {
        return $this->data['category'];
    }

    public function view(): View
    {
        $view = view('warehouse.product.catalog', ['data' => $this->data['products'], 'client' => $this->client]);

        return $view;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class=> function(AfterSheet $event) {
                $cellRange = 'B3:J3'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(8);
                $event->sheet->getStyle($cellRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('002060'); /*Background*/
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()->setRGB('fffff');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical('center');
                $event->sheet->getDelegate()->getRowDimension('3')->setRowHeight(54);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(1);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(32);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(31);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(17);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(12);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(51);

                $event->sheet->getCell('A1')->getHyperlink()->setUrl("sheet://'PRODUCTOS'!A1");
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setShowGridlines(false);
        $totalRows = $sheet->getHighestRow();
        $estilo = [
            'alignment' => [
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000'],
                ]
            ],
        ];
        $sheet->getStyle("B4:J{$totalRows}")->applyFromArray($estilo);

        // HEADER
        $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(19)->getColor()->setARGB('002060');
        $sheet->getStyle('D2')->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF0000');
        $sheet->getStyle('D1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('H1')->getFont()->setBold(true)->getColor()->setARGB('FF0000');
        $sheet->getStyle('H1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('H1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('J1')->getFont()->setBold(true)->setSize(10)->getColor()->setARGB('FF0000');
        $sheet->getStyle('J1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('J1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = $sheet->getRowIterator(3, 3)->current();
        $estilo_fila = $sheet->getStyle($headers->getRowIndex());
        $estilo_fuente = [
            'font' => [
                'bold' => true,
                'size' => 8,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['rgb' => 'ffffff'],
                ]
            ],
        ];
        $estilo_fila->applyFromArray($estilo_fuente);

        // COLUMN B
        $columnBBackground = [
            'font' => [
                'size' => 12,
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle("B4:B{$totalRows}")->applyFromArray($columnBBackground);

        // COLUMN C
        $rangeImageColumnC = "C4:C{$totalRows}";

        $alignImage = [
            'alignment' => [
                'wrapText' => true,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'indent' => 1,
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                    'color' => ['argb' => 'FF000000'],
                ],
                'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF000000']],
            ],
        ];
        $sheet->getStyle($rangeImageColumnC)->applyFromArray($alignImage);

        // COLUMN D

        $columnD = [
            'font' => [
                'size' => 12,
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle("D4:D{$totalRows}")->applyFromArray($columnD);

        $columnE = [
            'font' => [
                'size' => 9,
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle("E4:E{$totalRows}")->applyFromArray($columnE);

        // COLUMN F
        $columnF = [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '8DB4E2',
                ],
            ],
            'font' => [
                'size' => 12,
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle("F4:F{$totalRows}")->applyFromArray($columnF);

        // COLUMNS PRICES
        $columnPrices = [
            'font' => [
                'size' => 14,
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle("G4:G{$totalRows}")->applyFromArray($columnPrices);
        $sheet->getStyle("H4:H{$totalRows}")->applyFromArray($columnPrices);
        $sheet->getStyle("I4:I{$totalRows}")->applyFromArray($columnPrices);

        // COLUMN J
        $columnJ = [
            'font' => [
                'size' => 9,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle("J4:J{$totalRows}")->applyFromArray($columnJ);
    }

//    public function drawings()
//    {
//        // Agrega la forma en la celda A1
//        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
//        $drawing->setName('Flecha roja');
//        $drawing->setDescription('Flecha roja');
//        $drawing->setPath(public_path('images/arrow-left.png'));
//        $drawing->setCoordinates('J1');
//        $drawing->setOffsetX(5);
//        $drawing->setOffsetY(20);
//        $drawing->setHeight(90);
//        $drawing->setHyperlink(new Hyperlink("sheet://'PRODUCTOS'!A1"));
//
//        // Retorna las formas
//        return [$drawing];
//    }

    public function headings(): array
    {

    }
}
