<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportDailyExport implements FromView,ShouldAutoSize, WithEvents, WithStyles
{
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('commercial.reports.daily.excel', ['data' => $this->data]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class=> function(AfterSheet $event) {
                $cellRange = 'A2:M2'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(8);
                $event->sheet->getStyle('A2:I2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('92D050');
                $event->sheet->getStyle('J2:M2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('595959');
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(35);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()->setRGB('ffffff');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical('center');
//                $event->sheet->getDelegate()->getStyle()
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(11)->getColor()->setARGB('92D050');
        $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(11);
        $sheet->setShowGridlines(false);
        $totalRows = $sheet->getHighestRow();
        $estilo = [
            'alignment' => [
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '92D050'],
                ]
            ],
            'font' => [
                'size' => 7.5,
            ],
        ];
        $sheet->getStyle("A3:M{$totalRows}")->applyFromArray($estilo);

        $columnBBackground = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle("A1:E1")->applyFromArray($columnBBackground);
    }
}