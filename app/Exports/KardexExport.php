<?php

namespace App\Exports;

use App\Kardex;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;

class KardexExport implements FromView,WithHeadings,ShouldAutoSize, WithEvents
{
    use Exportable;

    public $product;
    public $kardexs;
    public $warehouse;
    public $type;

    public function __construct($kardexs, $product, $warehouse, $type)
    {
        $this->product = $product;
        $this->kardexs = $kardexs;
        $this->warehouse = $warehouse;
        $this->type = $type;
    }

    public function view(): View
    {
        $data = [
            'kardexs' => $this->kardexs,
            'product' => $this->product,
            'warehouse' => $this->warehouse,
        ];
        if ($this->type == 1) {
            return view('warehouse.kardex.excel', $data);
        } else if ($this->type == 2) {
            return view('accountancy.kardex.excel', $data);
        }else{
            return view('accountancy.kardex-fisic.excel', $data);
        }
    }

    public function headings(): array
    {
        //
    }

    public function registerEvents(): array
    {
        if ($this->type == 1) {
            return [
                AfterSheet::class=> function(AfterSheet $event) {
                    $cellRange = 'A1:G1'; // All headers
                    $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                    $event->sheet->getStyle($cellRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('000000'); /*Background*/
                    $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()->setRGB('fffff');
                    $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                    $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal('center');
                    $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical('center');
                }
            ];
        } else {
            return [];
        }
    }
}
