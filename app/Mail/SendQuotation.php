<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Quotation;
use App\QuotationDetail;

class SendQuotation extends Mailable
{
    use Queueable, SerializesModels;
    public $id;
    public $file;
    public $clientInfo;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id, $file,$clientInfo)
    {
        $this->id = $id;
        $this->file = $file;
        $this->clientInfo = $clientInfo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $quotation = Quotation::find($this->id);
        //$quotation_detail = QuotationDetail::where('quotation_id', $this->id)->get();
        $data = array(
            'quotation'         =>  $quotation,
            'clientInfo'        =>  $this->clientInfo
        );


        return $this->subject('COTIZACIÓN ' . $quotation->serial_number . ' - '  . $quotation->correlative . ' | ' .$this->clientInfo->business_name)
            ->view('commercial.quotation.mail')->with($data)
            ->attachData($this->file->output(), 'Cotización ' . $quotation->serial_number . ' - ' . $quotation->correlative.'.pdf');
    }
}
