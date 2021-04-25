<?php

namespace App\Console\Commands;

use App\Services\GoogleFitService;
use Illuminate\Console\Command;

class FetchGoogleFit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mygooglefit:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data from Google Fit';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Fetching Google Fit step data');

        $weight = GoogleFitService::make()->getWeight();
        $step = GoogleFitService::make()->getStepCount();
        $calories = GoogleFitService::make()->getCaloriesExpended();
        $sleep = GoogleFitService::make()->getSleepHoursCount();
        $heartRate = GoogleFitService::make()->getHeartRate();
        $this->info("Weight : $weight kg.");
        $this->info("Step Count : $step step(s).");
        $this->info("Calories Expended : $calories cal.");
        $this->info("Sleep Hours : $sleep hr(s).");
        $this->info("Heart Rate : $heartRate bpm.");
    }
}
