<?php

namespace Database\Seeders;

use App\Models\ListLangue;
use App\Models\Person;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NotifierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $language = ListLangue::where('llangue_acronyn', env('NOTIFICATOR_LANG'))->first();
        if(!$language)
            return;
        Person::firstOrCreate([
            'person_username' => env('NOTIFICATOR_NAME'),
            'person_email'    => env('NOTIFICATOR_EMAIL'),
            'person_password' => Hash::make(env('NOTIFICATOR_PASS')),
            'person_langue'   => $language->llangue_id,
        ]);
    }
}
