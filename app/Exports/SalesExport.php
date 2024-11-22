<?php

namespace App\Exports;

use App\Sale;
use App\DebitNote;
use App\CreditNote;
use App\LowCommunication;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SalesExport implements FromView, ShouldAutoSize, WithEvents, WithStyles
{
    public $from;
    public $to;
    public $headquarter;
    public $status;

    public function __construct($to, $from, $headquarter, $status)
    {
        $this->to = $to;
        $this->from = $from;
        $this->headquarter = $headquarter;
        $this->status = $status;
    }

    public function view(): View
    {
        return view('commercial.sale.excel', [
            'sales' => Sale::with('sunat_code')
                            ->where('client_id',auth()->user()->headquarter->client_id)
                            ->whereBetween('date',  [$this->from, $this->to])
                            ->where(function ($query) {
                                if ($this->status == '2') {
                                    $query->where('expiration', '>=', date('Y-m-d'))
                                        ->where('status_condition', 0)
                                        ->where('paidout', '!=', 1);
                                }
            
                                if ($this->status == '3') {
                                    $query->whereBetween('expiration',  [$this->from, date('Y-m-d')]);
                                    $query->where('status_condition', 0)
                                        ->where('paidout', '!=', 1);
                                }

                                if ($this->status == '4') {
                                    $query->where('status_condition', 1)
                                        ->where('paidout', 1);
                                }
                            })
                            ->get(),
        ]);
    }

    public function headings(): array
    {
        
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class=> function(AfterSheet $event) {
                $cellRange = 'A1:X1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getStyle($cellRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('a9c242'); /*Background*/
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()->setRGB('fffff');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical('center');
//                $event->sheet->getDelegate()->getStyle()
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:X1')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('ffffff');
        $sheet->setShowGridlines(false);
        $totalRows = $sheet->getHighestRow();
        $estilo = [
            'alignment' => [
                'wrapText' => false,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'a9c242'],
                ]
            ],
            'font' => [
                'size' => 10,
            ],
        ];

        $estilo2 = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'fdfdfd'],
                ]
            ]
        ];

        $columnBBackground = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $newRows = $totalRows -1;
        $sheet->getStyle("A2:X{$newRows}")->applyFromArray($estilo);
        $sheet->getStyle("K{$newRows}:S{$totalRows}")->applyFromArray($estilo);
        $sheet->getStyle("A1:X1")->applyFromArray(array_merge($estilo2,$columnBBackground));

    }
}
