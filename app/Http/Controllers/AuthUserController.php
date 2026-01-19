<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests\LoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class AuthUserController extends Controller
{

    public function register(UserRegisterRequest $request)
    {
        $data = $request->validated();

        // Hash password manually
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'data' => $user
        ], 201);
    }

 public function login(LoginRequest $request)
{
    $credentials = $request->only('email', 'password');

    if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => auth('api')->user()
        ]);
}

}
