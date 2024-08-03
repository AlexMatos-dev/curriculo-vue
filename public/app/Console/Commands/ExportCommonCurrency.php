<?php

namespace App\Console\Commands;

use App\Http\Controllers\ExportController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class ExportCommonCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-common-currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports all common currencies into a JSON file at: storage/app/exports/currencies.json';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Exporting currencies!');
        $exportController = new ExportController(new Request());
        $exportController->exportCommonCurrencies();
    }
}
