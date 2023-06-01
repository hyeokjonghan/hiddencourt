<?php

namespace App\Http\Controllers\Ms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth2\Client\Provider\GenericProvider;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class OneDriveController extends Controller
{
    public function getOauth() {
        return Socialite::driver('microsoft')->redirect();
    }

    public function redirect() {
        return Socialite::driver('microsoft')->user();
    }


    // FILE UPLOAD (return 값에 따라서 GET FILE INFO정보 달라 질 듯)
        
    
}
