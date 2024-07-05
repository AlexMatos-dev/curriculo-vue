<?php

namespace App\Console\Commands;

use App\Http\Controllers\ExportController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class ExportTranslation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-translation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports all system translations into a JSON file at: sttorage/app/exports/translations.json';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Exporting translations!');
        $exportController = new ExportController(new Request());
        $exportController->exportTranslations();
    }
}
