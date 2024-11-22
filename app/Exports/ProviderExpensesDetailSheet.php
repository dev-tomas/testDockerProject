<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProviderExpensesDetailSheet implements FromView, WithHeadings,ShouldAutoSize, WithEvents, WithTitle
{
    public $sales;
    public $movements;
    public $voucher;
    public $sd;

    public function __construct($sales,$sd, $movements, $voucher)
    {
        $this->sales = $sales;
        $this->movements = $movements;
        $this->voucher = $voucher;
        $this->sd = $sd;
    }

    public function view(): View
    {
        return view('accountancy.purchasePayment.interfaz-xls', [
            'sales' => $this->sales,
            'sd' => $this->sd,
            'movements' => $this->movements,
            'voucher' => $this->voucher,
        ]);
    }

    public function headings(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class=> function(AfterSheet $event) {
                $cellRange = 'A1:J1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getStyle($cellRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('000000'); /*Background*/
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()->setRGB('fffff');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical('center');
            }
        ];
    }

    public function title(): string
    {
        return 'CAJAEGR_DET';
    }
}
