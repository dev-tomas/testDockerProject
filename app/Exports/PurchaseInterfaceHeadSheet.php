<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PurchaseInterfaceHeadSheet implements FromView, WithHeadings,ShouldAutoSize, WithEvents, WithTitle
{
    public $book;
    public $type;

    public function __construct($book, $type)
    {
        $this->book = $book;
        $this->type = $type;
    }

    public function view(): View
    {
        $data = [
            'book' => $this->book,
        ];
        if ($this->type == 1) {
            return view('accountancy.purchase.interfaz-head-xls', $data);
        } else {
            return view('accountancy.purchase.v2136.interfaz-head-xls', $data);
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
        return 'COMPRAS_CAB';
    }
}
