<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNoteCredit extends Mailable
{
    use Queueable, SerializesModels;

    public $cn;
    public $file;
    public $clientInfo;
    public $attach;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($cn,$file,$attach,$clientInfo)
    {
        $this->cn = $cn;
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
        $email = $this->view('commercial.sale.notes.mail1')
        ->subject('NOTA DE CREDITO ' . $this->cn->serial_number . ' - '  . $this->cn->correlative . ' | ' . $this->clientInfo->business_name)
        ->attachData($this->file->output(), 'Venta  ' . $this->cn->serial_number . ' - ' . $this->cn->correlative.'.pdf');

        foreach ($this->attach as $archivo) {
            $email->attach($archivo);
        }

        return $email;
    }
}
