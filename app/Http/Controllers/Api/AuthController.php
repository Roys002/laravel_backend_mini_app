<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\RefreshToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);
        // assign role default 'user'
        $user->assignRole('user');

        $token = $user->createToken('apptoken')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = $request->user();
        $accessToken = $user->createToken('auth_token')->plainTextToken;

        // generate refresh token
        $refreshToken = Str::random(64);
        RefreshToken::updateOrCreate(
            ['user_id' => $user->id],
            ['refresh_token' => $refreshToken]
        );

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => 3600 // contoh: 1 jam
        ]);
    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string'
        ]);

        $stored = RefreshToken::where('refresh_token', $request->refresh_token)->first();

        if (!$stored) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        $user = $stored->user;

        // buat access_token baru
        $accessToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $stored->refresh_token, // tetap pakai refresh_token lama
            'token_type' => 'bearer',
            'expires_in' => 3600
        ]);
    }


    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return ['message' => 'Logged out'];
    }
}
