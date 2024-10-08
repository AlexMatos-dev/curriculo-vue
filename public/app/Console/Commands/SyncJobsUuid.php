<?php

namespace App\Console\Commands;

use App\Models\JobList;
use Illuminate\Console\Command;

class SyncJobsUuid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-jobs-uuid';

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
        (new JobList())->syncAndSetJobUuid();
    }
}
