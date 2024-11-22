<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOC extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $file;
    public $clientInfo;
    public $provider;
    public $cotizacion;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $file, $clientInfo, $provider, $cotizacion)
    {
        $this->order = $order;
        $this->file = $file;
        $this->clientInfo = $clientInfo;
        $this->provider = $provider;
        $this->cotizacion = $cotizacion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('logistic.purchase.orders.mail')
                ->subject('Orden de Compra  ' . $this->order->serie . ' - ' . $this->order->correlative . ' | ' .$this->clientInfo->business_name )
                ->attachData($this->file->output(), 'Orden de Compra  ' . $this->order->serie . ' - ' . $this->order->correlative.'.pdf')
                ->attach($this->cotizacion);
    }
}
