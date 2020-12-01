<?php

namespace App\Services;

use App\SocialFacebookAccount;
use App\Models\User;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialFacebookAccountService
{
    public function createOrGetUser(ProviderUser $providerUser)
    {
        $account = SocialFacebookAccount::whereProvider('facebook')
            ->whereProviderUserId($providerUser->getId())
            ->first();
        if ($account) {
            return $account->user;
        } else {
            $account = new SocialFacebookAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider'         => 'facebook',
            ]);
            $user = User::whereEmail($providerUser->getEmail())->first();
            if (!$user) {
                $file = str_replace('type=normal', 'type=large', $providerUser->getAvatar());
                $user = User::create([
                    'email'    => $providerUser->getEmail(),
                    'name'     => $providerUser->getName(),
                    'profile_photo_path'   => $file,
                    'token' => $providerUser->token,
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
