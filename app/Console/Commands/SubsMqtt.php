<?php

namespace App\Console\Commands;

use App\Models\Ph;
use App\Models\Ppm;
use App\Models\Temperature;
use Exception;
use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;

class SubsMqtt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mymqtt:subs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscript MQTT Broker';

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
        $mqtt = MQTT::connection();
        echo sprintf("Listening...\n");

        // Subscribe Hidro Temp
        $mqtt->subscribe('hydro/temp', function (string $topic, string $message) {
            try {
                if ($message == null) throw new Exception("Message can't be null !");

                $data = json_decode($message);

                if ($data->temperature == null || floatval($data->temperature) > 100) throw new Exception("Temperature out of range !");

                echo sprintf("temp: %s ", $data->temperature);
                Temperature::create([
                    'microtime'     => $data->microtime ?? round(microtime(true) * 1000),
                    'temperature'   => $data->temperature
                ]);
            } catch (Exception $e) {
                echo sprintf('ERROR : %s', $e->getMessage());
            }
            echo PHP_EOL;
        }, 1);

        // Subscribe Hidro Ph
        $mqtt->subscribe('hydro/ph', function (string $topic, string $message) {

            try {
                if ($message == null) throw new Exception("Message can't be null !");

                $data = json_decode($message);

                if ($data->ph == null || intval($data->ph) > 14 || intval($data->ph) < 1) throw new Exception("Ph out of range !");

                echo sprintf("ph: %s", $data->ph);
                Ph::create([
                    'microtime' => $data->microtime ?? round(microtime(true) * 1000),
                    'ph' => $data->ph
                ]);
            } catch (Exception $e) {
                echo sprintf('ERROR : [%s]', $e->getMessage());
            }
            echo PHP_EOL;
        }, 1);

        // Subscribe Hidro Ph
        $mqtt->subscribe('hydro/ppm', function (string $topic, string $message) {

            try {
                if ($message == null) throw new Exception("Message can't be null !");

                $data = json_decode($message);

                if ($data->ppm == null || intval($data->ppm) > 1200 || intval($data->ppm) < 0) throw new Exception("PPM out of range !");

                echo sprintf("ppm: %s", $data->ppm);
                Ppm::create([
                    'microtime' => $data->microtime ?? round(microtime(true) * 1000),
                    'ppm'   => $data->ppm
                ]);
            } catch (Exception $e) {
                echo sprintf('ERROR : [%s]', $e->getMessage());
            }
            echo PHP_EOL;
        }, 1);

        $mqtt->loop(true);
    }
}
