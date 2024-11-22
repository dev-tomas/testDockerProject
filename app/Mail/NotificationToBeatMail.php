<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationToBeatMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sale;
    public $file;
    public $clientInfo;
    public $attach;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sale,$file,$attach,$clientInfo)
    {
        $this->sale = $sale;
        $this->file = $file;
        $this->clientInfo = $clientInfo;
        $this->attach = $attach;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('notifications.tobeat')
        ->subject('VENTA ' . $this->sale->serialnumber . ' - '  . $this->sale->correlative . ' | ' . $this->clientInfo->business_name)
        ->attachData($this->file, $this->sale->type_voucher->description . $this->sale->serialnumber . ' - ' . $this->sale->correlative.'.pdf')
        ->attachData($this->attach, $this->sale->client->document . '-' .  $this->sale->type_voucher->code . '-' . $this->sale->serialnumber . ' - '  . $this->sale->correlative . '.xml' );

        return $email;
    }
}
