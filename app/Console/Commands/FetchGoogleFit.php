<?php

namespace App\Console\Commands;

use App\Models\CalorieExpended;
use App\Models\HeartRate;
use App\Models\Sleep;
use App\Models\Step;
use App\Models\Weight;
use App\Services\GoogleFitService;
use Illuminate\Console\Command;

class FetchGoogleFit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mygooglefit:fetch {field?}';

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
        $field = $this->argument('field');

        switch ($field) {
            case 'weight':
                $this->info('Fetching Weight Data on Google Fit');
                $weight = GoogleFitService::make()->getWeight();

                Weight::create([
                    'microtime' => round(microtime(true) * 1000),
                    'weight'    => $weight
                ]);

                $this->info("Weight : $weight kg");
                break;

            case 'step':
                $this->info('Fetching Step Data on Google Fit');
                $step = GoogleFitService::make()->getStepCount();

                Step::create([
                    'microtime' => round(microtime(true) * 1000),
                    'step'    => $step
                ]);
                $this->info("Step Count: $step step(s)");
                break;

            case 'calorie':
                $this->info('Fetching Calorie Expended Data on Google Fit');
                $calorie = GoogleFitService::make()->getCaloriesExpended();

                CalorieExpended::create([
                    'microtime' => round(microtime(true) * 1000),
                    'calorie'    => $calorie
                ]);
                $this->info("Expended: $calorie cal");
                break;

            case 'sleep':
                $this->info('Fetching Sleep Data on Google Fit');
                $data_json = GoogleFitService::make()->getSleepHoursCount();
                if ($data_json != null && $data_json != '') {
                    $data = json_decode($data_json);

                    Sleep::createOrUpdate(
                        [
                            'start_microtime' => $data->start_microtime,
                            'end_microtime' => $data->end_microtime,
                        ],
                        [
                            'start_microtime' => $data->start_microtime,
                            'end_microtime' => $data->end_microtime,
                        ]
                    );
                }

                $this->info("Sleep Data: $data_json cal");
                break;

            case 'heartRate':
                $this->info('Fetching Heart Rate Data on Google Fit');
                $heartRate = GoogleFitService::make()->getHeartRate();

                HeartRate::create([
                    'microtime' => round(microtime(true) * 1000),
                    'rate'    => $heartRate
                ]);
                $this->info("Heart Rate: $heartRate bpm");
                break;

            default:
                $this->info('Fetching Google Fit data');

                $weight = GoogleFitService::make()->getWeight();
                $step = GoogleFitService::make()->getStepCount();
                $calories = GoogleFitService::make()->getCaloriesExpended();
                $sleep = GoogleFitService::make()->getSleepHoursCount();
                $heartRate = GoogleFitService::make()->getHeartRate();
                $this->info("Weight : $weight kg.");
                $this->info("Step Count : $step step(s).");
                $this->info("Calories Expended : $calories cal.");
                $this->info("Sleep Time : $sleep");
                $this->info("Heart Rate : $heartRate bpm.");

                Weight::create([
                    'microtime' => round(microtime(true) * 1000),
                    'weight'    => $weight
                ]);
                Step::create([
                    'microtime' => round(microtime(true) * 1000),
                    'step'    => $step
                ]);
                CalorieExpended::create([
                    'microtime' => round(microtime(true) * 1000),
                    'calorie'    => $calories
                ]);
                if ($sleep != null && $sleep != '') {
                    $data = json_decode($sleep);

                    Sleep::createOrUpdate(
                        [
                            'start_microtime' => $data->start_microtime,
                            'end_microtime' => $data->end_microtime,
                        ],
                        [
                            'start_microtime' => $data->start_microtime,
                            'end_microtime' => $data->end_microtime,
                        ]
                    );
                }
                HeartRate::create([
                    'microtime' => round(microtime(true) * 1000),
                    'rate'    => $heartRate
                ]);
                break;
        }
    }
}
