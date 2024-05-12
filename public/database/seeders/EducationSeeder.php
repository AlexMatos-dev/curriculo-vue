<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Education::create([
            'edcurriculum_id'   => 1,
            'eddegree'          => 'Bachelor',
            'edfield_of_study'  => 'Data Science',
            'edinstitution'     => 'Stanford',
            'edstart_date'      =>date("Y-m-d",  strtotime('05/01/2015')),
            'edend_date'        => date("Y-m-d",  strtotime('05/11/2020')),
            'eddescription'     => 'It is a long established fact that a reader will be distracted by the readable content of a page when'
        ]);
    }
}
