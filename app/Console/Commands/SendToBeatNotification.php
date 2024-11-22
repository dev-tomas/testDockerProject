<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendToBeatNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendnotification:overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia notificacion para los comprobantes que vencieron';

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
        (new \App\Http\Controllers\NotificationController())->emailExpiredSales();
    }
}
