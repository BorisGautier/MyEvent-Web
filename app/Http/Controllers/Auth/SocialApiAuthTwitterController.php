<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialTwitterAccountService;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialApiAuthTwitterController extends Controller
{
    public function twitterConnect(Request $request, SocialTwitterAccountService $service)
    {
        $token = $request->token;
        $secret = $request->secret;

        $user = $service->createOrGetUser($user = Socialite::driver('twitter')->userFromTokenAndSecret($token, $secret));
        if ($user->hasVerifiedEmail()) {
            auth()->login($user);
            $token = $user->createToken('MyEvent')->accessToken;
            $success['token'] = $token;
            $success['user'] = $user;
            return $this->sendResponse($success, 'Connexion réussie.');
        } else {
            return $this->sendError("Email not verified.", ['error' => 'Unauthorised'], 400);
        }
    }
}
