<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\GoogleFitService;
use Google_Client;
use Illuminate\Http\Request;

class GoogleApiTokenController extends Controller
{
    private Google_Client $googleClient;

    public function __construct()
    {
        $this->googleClient = GoogleFitService::getGoogleClient();
    }

    public function index(Request $request){
        $this->googleClient->fetchAccessTokenWithAuthCode($request->input('code'));
        Setting::updateOrCreate(
            ['setting_param' => config('app.my_google_fit.setting_token_param')],
            ['setting_value' => json_encode($this->googleClient->getAccessToken())]);

        return response()->json(['status'=>true, 'Google app access token has been updated.']);
    }
}
