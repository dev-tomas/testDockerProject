<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class verifyInvoiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia los comprobantes pendientes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new \App\Http\Controllers\Commands\SendInvoiceController())->consultCDRInvoice(date('Y-m-d'));
    }
}
