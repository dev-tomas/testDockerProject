<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SaleInterfaceSheet implements FromView, WithHeadings,ShouldAutoSize, WithEvents, WithTitle
{
    public $sales;
    public $movements;
    public $voucher, $ai, $ar, $creditnotes, $ab, $type;

    public function __construct($sales, $movements, $voucher, $ai, $ar,$ab, $creditnotes, $type)
    {
        $this->sales = $sales;
        $this->movements = $movements;
        $this->voucher = $voucher;
        $this->ai = $ai;
        $this->ar = $ar;
        $this->ab = $ab;
        $this->creditnotes = $creditnotes;
        $this->type = $type;
    }

    public function view(): View
    {
        $data = [
            'sales' => $this->sales,
            'movements' => $this->movements,
            'voucher' => $this->voucher,
            'ai' => $this->ai,
            'ar' => $this->ar,
            'ab' => $this->ab,
            'creditnotes' => $this->creditnotes
        ];
        if ($this->type == 1) {
            return view('accountancy.sale.interfaz-xls', $data);
        } else {
            return view('accountancy.sale.v2136.interfaz-xls', $data);
        }
    }

    public function headings(): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class=> function(AfterSheet $event) {
                $cellRange = 'A1:Z1'; // All headers
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
        return 'VENTAS_DET';
    }
}
