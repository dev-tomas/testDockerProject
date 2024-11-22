<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportShoppingExport implements FromView,WithHeadings,ShouldAutoSize, WithEvents
{
    public $date;
    public $headquarter;
    public $product;
    public $desde;
    public $hasta;
    public $provider;
    public $clientInfo;
    public $shoppings;

    public function __construct($date,$shoppings, $headquarter, $product, $desde, $hasta, $provider, $clientInfo)
    {
        
        $this->date = $date;
        $this->headquarter = $headquarter;
        $this->product = $product;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->provider = $provider;
        $this->hasta = $hasta;
        $this->clientInfo = $clientInfo;
        $this->shoppings = $shoppings;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('logistic.reports.excel', [
            'date' => $this->date,
            'headquarter' => $this->headquarter,
            'product' => $this->product,
            'desde' => $this->desde,
            'hasta' => $this->hasta,
            'provider' => $this->provider,
            'clientInfo' => $this->clientInfo,
            'shoppings' => $this->shoppings,
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
                $cellRange = 'A3:G3'; // All headers
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
