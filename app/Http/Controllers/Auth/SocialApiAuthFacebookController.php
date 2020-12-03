<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialFacebookAccountService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialApiAuthFacebookController extends BaseController
{
    public function facebookConnect(Request $request, SocialFacebookAccountService $service)
    {
        $token = $request->token;

        $user = $service->createOrGetUser(Socialite::driver('facebook')->userFromToken($token));

        auth()->login($user);
        $token = $user->createToken('MyEvent')->accessToken;
        $success['token'] = $token;
        $success['user'] = $user;
        return $this->sendResponse($success, 'Connexion réussie.');
    }
}
