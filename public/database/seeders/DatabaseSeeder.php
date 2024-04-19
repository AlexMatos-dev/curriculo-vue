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
        // if(empty(User::where('email', 'root@administrator.com')->get())){
        //     User::factory()->create([
        //         'name' => 'Root Admin',
        //         'email' => 'root@administrator.com'
        //     ]);
        // }

        (new GenderSeeder())->run();
        (new LanguageSeeder())->run();
        (new CountrySeeder())->run();
        (new ProfileSeeder())->run();
    }
}
