<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Fitness;
use Google_Service_Fitness_AggregateBy;
use Google_Service_Fitness_AggregateRequest;
use Google_Service_Fitness_BucketByTime;
use Google_Service_Fitness_Session as Session;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GoogleFitService
{
    public const CREDENTIALS_PATH = 'google-fit-access.json';
    public const AUTH_PATH = 'client_id.json';
    public const AUTH_PARAM = 'GOOGLE_FIT_TOKEN';
    public const ACTIVITY_TYPE_SLEEP = 72;

    private Google_Client $googleClient;

    private Google_Service_Fitness $client;

    public function __construct()
    {
        // $tokenPath = base_path(self::CREDENTIALS_PATH);
        $tokenData = Setting::find(self::AUTH_PARAM);
        $authConfig = base_path(self::AUTH_PATH);

        $this->googleClient = app(Google_Client::class);
        $this->googleClient->setApplicationName('LMP IoT Dashboard');
        $this->googleClient->setScopes([
            Google_Service_Fitness::FITNESS_ACTIVITY_READ,
            Google_Service_Fitness::FITNESS_SLEEP_READ,
            Google_Service_Fitness::FITNESS_HEART_RATE_READ,
            Google_Service_Fitness::FITNESS_OXYGEN_SATURATION_READ,
            Google_Service_Fitness::FITNESS_NUTRITION_READ,
            Google_Service_Fitness::FITNESS_BODY_READ
        ]);
        $this->googleClient->setAuthConfig($authConfig);
        $this->googleClient->setAccessType('offline');
        $this->googleClient->setPrompt('select_account consent');

        if ($tokenData != null && $tokenData->setting_value != null) {
            $accessToken = json_decode($tokenData->setting_value, true);
            $this->googleClient->setAccessToken($accessToken);
        }

        if ($this->googleClient->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($this->googleClient->getRefreshToken()) {
                $this->googleClient->fetchAccessTokenWithRefreshToken($this->googleClient->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $this->googleClient->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));

                // Exchange authorization code for an access token.
                $accessToken = $this->googleClient->fetchAccessTokenWithAuthCode($authCode);
                $this->googleClient->setAccessToken($accessToken);

                // Check to see if there was an error.
                if (array_key_exists('error', $accessToken)) {
                    throw new Exception(join(', ', $accessToken));
                }
            }
            // Save the token to a file.
            // if (!file_exists(dirname($tokenPath))) {
            //     mkdir(dirname($tokenPath), 0700, true);
            // }
            // file_put_contents($tokenPath, json_encode($this->googleClient->getAccessToken()));

            Setting::updateOrCreate(['setting_param' => self::AUTH_PARAM], ['setting_value' => json_encode($this->googleClient->getAccessToken())]);
        }

        $this->client = new Google_Service_Fitness($this->googleClient);
    }

    public static function make(): self
    {
        return new static();
    }

    public function getSleepHoursCount()
    {
        $response = $this->client->users_sessions->listUsersSessions('me');

        return collect($response->getSession())->filter(static function (Session $session) {
            return $session->getActivityType() === self::ACTIVITY_TYPE_SLEEP;
        })->reverse()
            ->filter(static function (Session $session) {
                return Carbon::parse((int) ($session->getEndTimeMillis() / 1000))->timezone(config('app.timezone'))->isToday();
            })
            ->map(static function (Session $session) {
                $startTime = Carbon::parse(intval($session->getStartTimeMillis() / 1000), 'UTC');
                $endTime = Carbon::parse(intval($session->getEndTimeMillis() / 1000), 'UTC');
                $diff = $endTime->diff($startTime);
                return json_encode([
                    "hour" => intval($diff->format('%h')),
                    "minute" => intval($diff->format('%i')),
                    "second" => intval($diff->format('%s')),
                ]);
            })->first() ?? 0;
    }

    public function getStepCount(): int
    {
        $request = new Google_Service_Fitness_AggregateRequest();

        $request->setAggregateBy([
            new Google_Service_Fitness_AggregateBy([
                'dataTypeName' => 'com.google.step_count.delta',
                'dataSourceId' => 'derived:com.google.step_count.delta:com.google.android.gms:estimated_steps',
            ]),
        ]);

        $request->setBucketByTime(new Google_Service_Fitness_BucketByTime([
            'durationMillis' => 86400000,
        ]));

        $request->setStartTimeMillis(Carbon::today()->startOfDay()->timestamp * 1000);
        $request->setEndTimeMillis(Carbon::today()->endOfDay()->timestamp * 1000);

        $response = $this->client->users_dataset->aggregate('me', $request);

        return $response->getBucket()[0]->getDataset()[0]->getPoint()[0]->getValue()[0]->getIntVal() ?? 0;
    }

    public function getCaloriesExpended()
    {
        $request = new Google_Service_Fitness_AggregateRequest();

        $request->setAggregateBy([
            new Google_Service_Fitness_AggregateBy([
                'dataTypeName' => 'com.google.calories.expended',
                'dataSourceId' => 'derived:com.google.calories.expended:com.google.android.gms:merge_calories_expended',
            ]),
        ]);

        $request->setBucketByTime(new Google_Service_Fitness_BucketByTime([
            'durationMillis' => 86400000,
        ]));

        $request->setStartTimeMillis(Carbon::today()->startOfDay()->timestamp * 1000);
        $request->setEndTimeMillis(Carbon::today()->endOfDay()->timestamp * 1000);

        $response = $this->client->users_dataset->aggregate('me', $request);

        return $response->getBucket()[0]->getDataset()[0]->getPoint()[0]->getValue()[0]->getFpVal() ?? 0;
    }

    public function getWeight()
    {
        $request = new Google_Service_Fitness_AggregateRequest();

        $request->setAggregateBy([
            new Google_Service_Fitness_AggregateBy([
                'dataTypeName' => 'com.google.weight',
                'dataSourceId' => 'derived:com.google.weight:com.google.android.gms:merge_weight',
            ]),
        ]);

        $request->setBucketByTime(new Google_Service_Fitness_BucketByTime([
            'durationMillis' => 86400000,
        ]));

        $request->setStartTimeMillis(Carbon::today()->startOfDay()->timestamp * 1000);
        $request->setEndTimeMillis(Carbon::today()->endOfDay()->timestamp * 1000);

        $response = $this->client->users_dataset->aggregate('me', $request);

        return $response->getBucket()[0]->getDataset()[0]->getPoint()[0]->getValue()[0]->getFpVal() ?? 0;
    }

    public function getHeartRate()
    {
        $request = new Google_Service_Fitness_AggregateRequest();

        $request->setAggregateBy([
            new Google_Service_Fitness_AggregateBy([
                'dataTypeName' => 'com.google.heart_rate.bpm',
                'dataSourceId' => 'derived:com.google.heart_rate.bpm:com.google.android.gms:merge_heart_rate_bpm',
            ]),
        ]);

        $request->setBucketByTime(new Google_Service_Fitness_BucketByTime([
            'durationMillis' => 86400000,
        ]));

        $request->setStartTimeMillis(Carbon::today()->startOfDay()->timestamp * 1000);
        $request->setEndTimeMillis(Carbon::today()->endOfDay()->timestamp * 1000);

        $response = $this->client->users_dataset->aggregate('me', $request);

        return (int) $response->getBucket()[0]->getDataset()[0]->getPoint()[0]->getValue()[0]->getFpVal() ?? 0;
    }
}
