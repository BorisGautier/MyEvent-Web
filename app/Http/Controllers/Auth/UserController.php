<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class UserController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);


        if ($validator->fails()) {
            return $this->sendError('Erreur de paramètres.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user->sendEmailVerificationNotification();
        $success['token'] = $user->createToken('MyEvent')->accessToken;
        // $success['name'] = $user->name;
        $success['user'] = $user;

        if ($user) {
            return $this->sendResponse($success, 'Création réussie.');
        } else {
            return $this->sendError("Pas autorisé.", ['error' => 'Unauthorised']);
        }
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $user = User::find($user->id);
            if ($user->hasVerifiedEmail()) {
                $success['token'] = $user->createToken('MyEvent')->accessToken;
                $success['user'] = $user;

                return $this->sendResponse($success, 'Connexion réussie.');
            } else {
                return $this->sendError("Email not verified.", ['error' => 'Unauthorised'], 400);
            }
        } else {
            return $this->sendError('Pas autorisé.', ['error' => 'Unauthorised']);
        }
    }

    public function logout()
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $token = $user->token();
        $token->revoke();

        return $this->sendResponse("", 'Deconnexion réussie.');
    }

    public function getUser()
    {
        $user = Auth::user();

        if ($user) {
            $success["user"] = $user;

            return $this->sendResponse($success, 'Utilisateur');
        } else {
            return $this->sendError('Pas autorisé.', ['error' => 'Unauthorised']);
        }
    }

    /* public function getUserById(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);

        if ($user) {

            $success["user"] = $user;

            return $this->sendResponse($success, 'Utilisateur');
        } else {
            return $this->sendError('Pas autorisé.', ['error' => 'Unauthorised']);
        }
    }*/

    public function updateUser(Request $request)
    {
        $user = Auth::user();
        $user = User::find($user->id);

        $user->name = $request->name ?? $user->name;
        $user->telephone = $request->telephone ?? $user->telephone;
        $user->fcmToken = $request->fcmToken ?? $user->fcmToken;
        $user->platform = $request->platform ?? $user->platform;

        if ($request->file('avatar')->isValid()) {
            $extension = $request->avatar->extension();
            $path      = $request->avatar->storeAs($user->name . '/avatar', 'avatar.' . $extension, 'public');
            $url       =  $user->name . '/avatar/' . 'avatar.' . $extension;
        }

        $user->profile_photo_path = $url ?? $user->profile_photo_path;



        $save = $user->save();

        if ($save) {
            $success["user"] = $user;
            return $this->sendResponse($success, "Utilisateur");
        } else {
            return $this->sendError("Echec de mise à jour", ['error' => 'Unauthorised']);
        }
    }

    public function forgot()
    {
        $credentials = request()->validate(['email' => 'required|email']);

        Password::sendResetLink($credentials);

        return $this->sendResponse("", "Un lien de reinitialisation vous a été envoyé par mail.");
    }

    public function reset()
    {
        $credentials = request()->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|confirmed'
        ]);

        $reset_password_status = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        if ($reset_password_status == Password::INVALID_TOKEN) {
            return $this->sendError("Invalid token provided", ['error' => 'Unauthorised']);
        }

        return $this->sendResponse("", "Password has been successfully changed");
    }
}
