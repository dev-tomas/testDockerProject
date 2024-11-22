<?php

namespace App\Exports;

use App\PurchaseOrder;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PurchaseOrdersExports implements FromView,WithHeadings,ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('logistic.purchase.orders.excel', [
            'orders' => PurchaseOrder::where('client_id', auth()->user()->headquarter->client_id)->get()
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
                $cellRange = 'A1:H1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getStyle($cellRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('000000'); /*Background*/
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()->setRGB('fffff');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setVertical('center');
//                $event->sheet->getDelegate()->getStyle()
            }
        ];
    }
}