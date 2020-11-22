<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


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
        $token = $user->token();
        $token->revoke();

        return response()->json(array(
            'status' => 'success',
        ), 200);
    }

    public function getUser()
    {
        $user = Auth::user();

        if ($user) {

            return response()->json(array(
                'user' => $user,
                'status' => 'success',
            ), 200);
        } else {
            return $this->sendError('Pas autorisé.', ['error' => 'Unauthorised']);
        }
    }

    public function getUserById($id)
    {
        $user = User::find($id);

        if ($user) {

            return response()->json(array(
                'user' => $user,
                'status' => 'success',
            ), 200);
        } else {
            return $this->sendError('Pas autorisé.', ['error' => 'Unauthorised']);
        }
    }
}
