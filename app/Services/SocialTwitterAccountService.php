<?php

namespace App\Services;

use App\SocialTwitterAccount;
use App\Models\User;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialTwitterAccountService
{
    public function createOrGetUser(ProviderUser $providerUser)
    {
        $account = SocialTwitterAccount::whereProvider('twitter')
            ->whereProviderUserId($providerUser->getId())
            ->first();
        if ($account) {
            return $account->user;
        } else {
            $account = new SocialTwitterAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider'         => 'twitter',
            ]);
            $user = User::whereEmail($providerUser->getEmail())->first();
            if (!$user) {
                $file = str_replace('_normal', '', $providerUser->getAvatar());
                $user = User::create([
                    'email'    => $providerUser->getEmail() ?? "No email",
                    'name'     => $providerUser->getName(),
                    'profile_photo_url'   => $file,
                    'token' => $providerUser->token,
                    'token_secret' => $providerUser->tokenSecret,
                    'password' => md5(rand(1, 10000)),
                ]);
                $user->sendEmailVerificationNotification();
            }
            $account->user()->associate($user);
            $account->save();
            return $user;
        }
    }
}
