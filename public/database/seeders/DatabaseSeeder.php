<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        (new SystemTranslationSeeder())->run();
        (new CompanySocialNetworkTypeSeeder())->run();
        (new AreaOfStudySeeder())->run();
        (new DegreeTypeSeeder())->run();
        (new ProficiencySeed())->run();
        (new TagsSeeder())->run();
        (new VisasTypeSeeder())->run();
        (new JobModalitySeeder())->run();
        (new ListProfessionSeeder())->run();
        (new GenderSeeder())->run();
        (new LanguageSeeder())->run();
        (new CountrySeeder())->run();
        (new BrStateSeeder)->run();
        (new BrCitySeeder)->run();
        (new CurriculumSeeder())->run();
        (new CummonCurrencySeeder())->run();
    }
}
