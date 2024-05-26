<?php

namespace Database\Seeders;

use App\Models\JobApplied;
use App\Models\JobList;
use App\Models\Professional;
use Illuminate\Database\Seeder;

class CreateJobApplications extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $limit = false;
        while(!is_numeric($limit)){
            $limit = $this->command->ask('Enter the number of job applications records to create');
        }
        if($limit < 1)
            $limit = 1;
        $professional = null;
        while(!$professional){
            $id = $this->command->ask('Enter the professional id');
            $professional = Professional::find($id);
            if(!$professional){
                $this->command->newLine(1);
                $this->command->line('Invalid professional id');
            }
        }
        $jobs = JobList::all()->toArray();
        $jobAppliedObj = new JobApplied();
        $status = $jobAppliedObj->getStatus();
        $myApplications = $jobAppliedObj->where('professional_id', $professional->professional_id)->get();
        $usedJobIds = [];
        foreach($myApplications as $application){
            if(!array_key_exists($application->job_id, $usedJobIds))    
                $usedJobIds[$application->job_id] = $application->job_id;
        }
        $index = 1;
        $saved = 0;
        if(count($jobs) > count($usedJobIds)){
            foreach($jobs as $job){
                if($index >= $limit)
                    break;
                if(in_array($job['job_id'], $usedJobIds))
                    continue;
                $thisJobId = $job['job_id'];
                if(JobApplied::where('professional_id', $professional->professional_id)->where('job_id', $thisJobId)->first()){
                    $usedJobIds[] = $thisJobId;
                    continue;
                }
                JobApplied::create([
                    'job_id' => $thisJobId,
                    'professional_id' => $professional->professional_id,
                    'status' => $status[array_rand($status)]
                ]);
                $saved++;
                $index++;
            }
        }
        if($saved == 0){
            $this->command->newLine(1);
            $this->command->line('Professional already applied to all avaliable jobs');
        }
    }
}
