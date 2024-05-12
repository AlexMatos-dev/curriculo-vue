<?php

namespace Database\Seeders;

use App\Models\Experience;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Experience::create([
           'excurriculum_id'=>1,
           'exjob_title'    =>'Programador',
           'excompany_name' => 'Apple',
           'exstart_date'   => date("Y-m-d",  strtotime('05/11/2020')),
           'exend_date'     => date("Y-m-d",  strtotime('20/07/2022')),
           'exdescription' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters,'
       ]);
    }
}
