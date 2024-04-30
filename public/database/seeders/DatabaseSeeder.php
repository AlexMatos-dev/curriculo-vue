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
        (new JobModalitySeeder())->run();
        (new ListProfessionalSeeder())->run();
        (new GenderSeeder())->run();
        (new LanguageSeeder())->run();
        (new CountrySeeder())->run();
        // (new ProfileSeeder())->run();
        (new BrStateSeeder)->run();
        (new BrCitySeeder)->run();
    }
}
