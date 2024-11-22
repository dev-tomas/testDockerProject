<?php

namespace App\Exports;

use App\PurchaseCredit;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PurchaseCreditExport implements FromView,WithHeadings,ShouldAutoSize, WithEvents
{
    public $from;
    public $to;
    public $client;
    public $status;

    public function __construct($to, $from, $client, $status)
    {
        $this->to = $to;
        $this->from = $from;
        $this->client = $client;
        $this->status = $status;
    }

    public function view(): View
    {
        $cus = $this->client;
        $sta = $this->status;
        return view('finances.providercredit.excel', [
            'credits' => PurchaseCredit::with('shopping', 'provider')
                                        ->where('client_id', auth()->user()->headquarter->client_id)
                                        ->where(function ($query) use ($cus, $sta) {
                                            if($cus != ''){
                                                $query->where('provider', $cus);
                                            }
                                            if($sta != ''){
                                                if ($sta == 2) {
                                                    $query->where('expiration', '<=', date('Y-m-d') );
                                                } else {
                                                    $query->where('status', $sta);
                                                }
                                            }
                                        })
                                        ->whereBetween('date', [$this->from, $this->to])->get(),
        ]);
    }

    public function headings(): array
    {
        
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
//                $event->sheet->getDelegate()->getStyle()
            }
        ];
    }
}
