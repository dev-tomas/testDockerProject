<?php

namespace App\Exports;

use App\Customer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomersExport implements FromView ,WithHeadings,ShouldAutoSize, WithEvents
{
    // /**
    // * @return \Illuminate\Support\Collection
    // */
    // public function collection()
    // {
    //     $data = Customer::select('typedocuments.code','customers.document','customers.description','customers.tradename','customers.address'
    //         ,'customers.email','customers.secondary_email','customers.phone','customers.detraction')
    //         ->join('typedocuments', 'customers.typedocument_id','=','typedocuments.id')
    //         ->get();
    //     return $data;
    // }

    public function view(): View
    {
        return view('commercial.customer.excel', [
            'customers' => Customer::with('document_type')->where('client_id', auth()->user()->headquarter->client_id)
                    ->get()
        ]);
    }

    public function headings(): array
    {
        return [
            'TIPO DE DOCUMENTO 
            6 = RUC 
            1 = DNI 
            - = VARIOS - VENTAS MENORES A S/.700.00 Y OTROS 
            4 = CARNET DE EXTRANJERÍA 
            7 = PASAPORTE 
            A = CÉDULA DIPLOMATICA DE IDENTIDAD 
            0 = NO DOMICILIADO, SIN RUC (EXPORTACIÓN)',
            'NUMERO',
            'RAZON SOCIAL',
            'RAZON_COMERCIAL (MARCA)',
            'DIRECCION',
            'CORREO 1',
            'CORREO 2',
            'TELÉFONO',
            'DETRACCIÓN'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class=> function(AfterSheet $event) {
                $cellRange = 'A1:M1'; // All headers
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