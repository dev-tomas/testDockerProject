<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Commands\ConsutlCPEController;

class ConsultCPEsOfDayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consult:cpeday';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consulta el estado de los comprobantes en la SUNAT emitidos durante el dia;';

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
        (new ConsutlCPEController())->consultAuthomaticOfDay();
    }
}
