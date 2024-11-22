<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOverdueNotification extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendnotification:tobeat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia notificacion para los comprobantes que estan por vencer';

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
        (new \App\Http\Controllers\NotificationController())->OverdueSales();
    }
}
