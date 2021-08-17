<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create(['name'     => $request->name,
                              'email'    => $request->email,
                              'is_admin' => false,
                              'password' => bcrypt($request->password)]);

        $response = ['access_token' => $user->createToken('authToken')->accessToken,
                     'id'           => $user->id,
                     'name'         => $user->name];

        return $this->sendCreated($response, 'User created');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return $this->sendUnauthorized('Incorrect user or password');
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeek();
        }
        $token->save();

        $response = ['access_token' => $tokenResult->accessToken,
                     'token_type'   => 'Bearer',
                     'expires_at'   => Carbon::parse($token->expires_at)->toDateTimeString()];
        return $this->sendResponse($response, 'Successful login');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return $this->sendSuccess('Successfully logged out');
    }
}
