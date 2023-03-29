<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(AuthLoginRequest $request)
    {
        if (!Auth::check()){
            if (($user = User::where('email', $request->email)->first()) && ($user->password === $request->password)){
                return response()->json([
                    "data" => [
                        "user_token" => $user->generateToken()
                    ]
                ]);
            }
        }
        return response()->json([
            "error" => [
                "code" => 401,
                "message" => "Authentication failed"
            ]
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::user()->update(['api_token' => null]);
        return response()->json([
            "data" => [
                "message" => "logout"
            ]
        ]);
    }

    public function role()
    {
        return response()->json([
            "data" => [
                "role" => Auth::user()->role
            ]
        ]);
    }
}
