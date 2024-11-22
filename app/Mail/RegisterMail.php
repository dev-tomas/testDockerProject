<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $clientInfo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($clientInfo)
    {
        $this->clientInfo = $clientInfo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('hola@gyosoluciones.com', 'Helmut de GYO MANAGER')->subject($this->clientInfo . ' Te doy la Bienvenidad a GYO MANAGER :)')
                    ->view('auth.mail-register');
    }
}
