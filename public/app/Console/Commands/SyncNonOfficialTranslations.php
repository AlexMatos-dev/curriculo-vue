<?php

namespace App\Console\Commands;

use App\Models\ListLangue;
use App\Models\Translation;
use Illuminate\Console\Command;

class SyncNonOfficialTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-non-official-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $languages    = ListLangue::all();
        $translations = Translation::all();
        foreach($translations as $translation){
            foreach($languages as $language){
                if(in_array($language->llangue_acronyn, Translation::OFFICIAL_LANGUAGES))
                    continue;
                try {
                    $translation->getTranslationByIsoCode($language->llangue_acronyn);
                } catch (\Throwable $th) {}
            }
        }
    }
}
