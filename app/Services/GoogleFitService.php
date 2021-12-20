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

class GoogleFitService
{
    public const AUTH_PATH = '';
    public const ACTIVITY_TYPE_SLEEP = 72;

    private Google_Service_Fitness $client;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $tokenData = Setting::find(config('app.my_google_fit.setting_token_param'));

        $googleClient = self::getGoogleClient();

        if ($tokenData != null && $tokenData->setting_value != null) {
            $accessToken = json_decode($tokenData->setting_value, true);
            $googleClient->setAccessToken($accessToken);
        }

        if ($googleClient->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($googleClient->getRefreshToken()) {
                $googleClient->fetchAccessTokenWithRefreshToken($googleClient->getRefreshToken());
            } else {
                // Request authorization from the user.
                $authUrl = $googleClient->createAuthUrl();
                throw new \Exception("Open the following link in your browser:\n\n$authUrl\n\n");
            }

        }
        $this->client = new Google_Service_Fitness($googleClient);
    }

    public static function make(): self
    {
        return new static();
    }

    public static function getAuthConfigPath(): string {
        $file_setting_path = config('app.my_google_fit.auth_config.path');
        $file_setting_name = config('app.my_google_fit.auth_config.file_name');

        return base_path("$file_setting_path/$file_setting_name");
    }

    public static function getGoogleClient(): Google_Client{
        $googleClient = app(Google_Client::class);
        $googleClient->setApplicationName('LMP IoT Dashboard');
        $googleClient->setScopes([
            Google_Service_Fitness::FITNESS_ACTIVITY_READ,
            Google_Service_Fitness::FITNESS_SLEEP_READ,
            Google_Service_Fitness::FITNESS_HEART_RATE_READ,
            Google_Service_Fitness::FITNESS_OXYGEN_SATURATION_READ,
            Google_Service_Fitness::FITNESS_NUTRITION_READ,
            Google_Service_Fitness::FITNESS_BODY_READ
        ]);
        $googleClient->setAuthConfig(self::getAuthConfigPath());
        $googleClient->setRedirectUri(route('google-app.set-access-token'));

        return $googleClient;
    }

    public function getSleepHoursCount(): string
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
                    "start_microtime" => $session->getStartTimeMillis(),
                    "end_microtime" => $session->getEndTimeMillis(),
                    "hour" => intval($diff->format('%h')),
                    "minute" => intval($diff->format('%i')),
                    "second" => intval($diff->format('%s')),
                ]);
            })->first() ?? '';
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
        if ($response->getBucket()[0]->getDataset()[0]->getPoint() == null) return 0;

        return $response->getBucket()[0]->getDataset()[0]->getPoint()[0]->getValue()[0]->getIntVal() ?? 0;
    }

    public function getCaloriesExpended(): float
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
        if ($response->getBucket()[0]->getDataset()[0]->getPoint() == null) return 0;

        return $response->getBucket()[0]->getDataset()[0]->getPoint()[0]->getValue()[0]->getFpVal() ?? 0;
    }

    public function getWeight(): float
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
        if ($response->getBucket()[0]->getDataset()[0]->getPoint() == null) return 0;

        return $response->getBucket()[0]->getDataset()[0]->getPoint()[0]->getValue()[0]->getFpVal() ?? 0;
    }

    public function getHeartRate(): int
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
        if ($response->getBucket()[0]->getDataset()[0]->getPoint() == null) return 0;

        return (int) $response->getBucket()[0]->getDataset()[0]->getPoint()[0]->getValue()[0]->getFpVal() ?? 0;
    }
}
