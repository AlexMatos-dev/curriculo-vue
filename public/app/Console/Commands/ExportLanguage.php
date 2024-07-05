<?php

namespace App\Console\Commands;

use App\Http\Controllers\ExportController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class ExportLanguage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:export-language';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports all languages into a JSON file at: storage/app/exports/languages.json';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Exporting languages!');
        $exportController = new ExportController(new Request());
        $exportController->exportLanguages();
    }
}
