<?php

namespace Database\Seeders;

use App\Models\Professional;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfessionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Professional::create([
            'person_id'=>1,
            'professional_slug'=>'teste',
            'professional_firstname' => 'teste',
            'professional_lastname' => 'teste',
            'professional_email' => 'teste',
            'professional_phone' => 'teste',
            'professional_photo' => 'teste',
            'professional_cover' => 'teste',
            'professional_title' => 'teste',
            'currently_working' => false,
            'avaliable_to_travel' => true,
            'paying' => true
        ]);
    }
}
