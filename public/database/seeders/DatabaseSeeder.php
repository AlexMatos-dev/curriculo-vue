<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if(empty(User::where('email', 'test@example.com')->get())){
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'adminstrator'
            ]);
        }

        (new GenderSeeder())->run();
        (new LanguageSeeder())->run();
        (new RoleSeeder())->run();
    }
}
