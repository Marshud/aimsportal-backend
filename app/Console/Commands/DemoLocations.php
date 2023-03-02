<?php

namespace App\Console\Commands;

use App\Models\Payam;
use App\Models\Project;
use Illuminate\Console\Command;

class DemoLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aims:demo-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'add-demo locations to projects';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $projects = Project::all();

        foreach($projects as $project) {
            $randomLocationCount = rand(1,5);
            $payams = Payam::inRandomOrder()->limit($randomLocationCount)->get();
            foreach($payams as $payam) {
                $project->locations()->updateOrCreate([
                    'state_id' => $payam->county->state->id,
                    'county_id' => $payam->county->id,
                    'payam_id' => $payam->id,
                    'ref' => 'state-id:'.$payam->county->state->id,
                ]);
            }
        }
        return Command::SUCCESS;
    }
}
