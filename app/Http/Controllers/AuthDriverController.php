<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverRegisterRequest;
use Illuminate\Http\Request;
use App\Models\Driver;

class AuthDriverController extends Controller
{
    //
    public function register(DriverRegisterRequest $request)
    {
        $data = $request->validated();

        // Hash password manually
        $data['password'] = bcrypt($data['password']);
        $driver = Driver::create($data);
        return response()->json([
            'status' => true,
            'message' => 'Driver registered successfully',
            'data' => $driver
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('driver-api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid driver credentials'], 401);
        }

        return response()->json([
            'status' => true,
            'token' => $token,
            'driver' => auth('driver-api')->user(),
        ]);
    }

}
