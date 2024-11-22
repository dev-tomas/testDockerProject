<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

class SendRequirementsPurchase extends Mailable
{
    use Queueable, SerializesModels;
    public $requirement;
    public $file;
    public $clientInfo;
    public $url;
    public $provider;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($requirement, $file, $clientInfo, $ruc, $provider)
    {
        $this->requirement = $requirement;
        $this->file = $file;
        $this->clientInfo = $clientInfo;
        $this->provider = $provider;
        $this->url = URL::signedRoute(
            'setQuotation',
            ['ruc' => $ruc, 'requirement' =>  $this->requirement->serie . '-' . $this->requirement->correlative]
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('logistic.purchase.mail')
                ->subject('Requerimiento  ' . $this->requirement->serie . ' - ' . $this->requirement->correlative . ' | ' .$this->clientInfo->business_name)
                ->attachData($this->file->output(), 'Requerimiento  ' . $this->requirement->serie . ' - ' . $this->requirement->correlative.'.pdf');;
    }
}
