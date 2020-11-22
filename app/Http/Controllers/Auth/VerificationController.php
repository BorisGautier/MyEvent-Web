<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends BaseController
{
    public function verify($id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return $this->sendError("Invalid/Expired url provided.", ['error' => 'Unauthorised']);
        }

        $user = User::findOrFail($id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        $success['user'] = $user;
        return $this->sendResponse($success, 'Vérification Reussie');
    }

    public function resend()
    {
        if (auth()->user()->hasVerifiedEmail()) {
            return $this->sendError("Email already verified.", ['error' => 'Unauthorised'], 400);
        }

        auth()->user()->sendEmailVerificationNotification();
        $success['user'] = auth()->user();
        return $this->sendResponse($success, 'Email verification link sent on your email id');
    }
}
