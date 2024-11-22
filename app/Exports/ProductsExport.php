<?php

namespace App\Exports;

use App\PriceList;
use App\Store;
use App\Warehouse;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductsExport implements FromView,WithHeadings,ShouldAutoSize, WithEvents
{
    public $headquarter;
    public function __construct()
    {
        $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : auth()->user()->headquarter_id;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;
        $pricesList = PriceList::get(['description', 'id']);

        return view('warehouse.product.excel', [
            'products' => Store::where('warehouse_id', $mainWarehouseId)->get(),
            'pricesList' => $pricesList
        ]);
    }

    public function headings(): array
    {
        // return [
        //     'TIPO DE DOCUMENTO
        //     6 = RUC 
        //     1 = DNI 
        //     - = VARIOS - VENTAS MENORES A S/.700.00 Y OTROS 
        //     4 = CARNET DE EXTRANJERÍA 
        //     7 = PASAPORTE 
        //     A = CÉDULA DIPLOMATICA DE IDENTIDAD 
        //     0 = NO DOMICILIADO, SIN RUC (EXPORTACIÓN)',
        //     'NUMERO',
        //     'DENOMINACIÓN',
        //     'RAZÓN_COMERCIAL
        //     (MARCA)',
        //     'DIRECCIÓN',
        //     'CORREO 1',
        //     'CORREO 2',
        //     'TELÉFONO',
        //     'DETRACCIÓN'
        // ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class=> function(AfterSheet $event) {
                $cellRange = 'A1:L1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getStyle($cellRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('a9c242'); /*Background*/
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()->setRGB('ffffff');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical('center');
//                $event->sheet->getDelegate()->getStyle()
            }
        ];
    }
}
