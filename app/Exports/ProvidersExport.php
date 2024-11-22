<?php

namespace App\Exports;

use App\Provider;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class ProvidersExport implements FromView, ShouldAutoSize, WithEvents, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    // {
    //     return Provider::where('client_id', auth()->user()->headquarter->client_id)->get(['description', 'document', 'phone', 'address', 'email', 'secondary_email', 'tradename', 'detraction', 'contact']);
    // }

    public function view(): View
    {
        return view('logistic.provider.excel', [
            'customers' => Provider::with('document_type')->where('client_id', auth()->user()->headquarter->client_id)->get()
        ]);
    }

    public function headings(): array
    {
        return [
            'Razón Social',
            'Documento',
            'Teléfono',
            'Dirección',
            'E-mail Principal',
            'E-mail Secundario',
            'Nombre Comercial',
            'Cuenta Detracción',
            'Contacto'
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:L1'; // All headers
                $event->sheet->getStyle($cellRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('a9c242'); /*Background*/
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(210);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()->setRGB('fffff');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical('center');
            }
        ];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('ffffff');
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
        $sheet->getStyle("A2:L{$totalRows}")->applyFromArray($estilo);
        $sheet->getStyle("A1:L1")->applyFromArray(array_merge($columnBBackground, $estilo2));
    }
    
}
