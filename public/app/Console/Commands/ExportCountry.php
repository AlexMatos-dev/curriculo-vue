<?php

namespace App\Console\Commands;

use App\Http\Controllers\ExportController;
use Illuminate\Console\Command;
use Illuminate\Http\Request as HttpRequest;

class ExportCountry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports all countries into a JSON file at: storage/app/exports/countries.json';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Exporting countries!');
        $exportController = new ExportController(new HttpRequest());
        $exportController->exportCountries();
    }
}
