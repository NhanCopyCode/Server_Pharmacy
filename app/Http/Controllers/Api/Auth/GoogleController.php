<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    //
    public function loginUrl() {}

    public function loginCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = null;
        $refreshToken = Str::random(60);

        DB::transaction(function () use ($googleUser, &$user, $refreshToken) {
            // Try to find user by social_id and social_provider
            $user = User::where('social_id', $googleUser->getId())
                ->where('social_provider', 'google')
                ->first();

            // If user doesn't exist, create new
            if (!$user) {
                $user = User::create([
                    'email' => $googleUser->getEmail(),
                    'name' => $googleUser->getName(),
                    'social_id' => $googleUser->getId(),
                    'social_provider' => 'google',
                    'password' => bcrypt(Str::random(12)), // dummy password
                    'refresh_token' => $refreshToken
                ]);
            }
        });

        // Generate JWT token (assumes JWTAuth is setup)
        $token = auth()->login($user);
        $user->refresh_token = $refreshToken;
        $user->save();

        // return response()->json([
        //     'access_token' => $token,
        //     'refresh_token' => $refreshToken,
        //     'token_type' => 'bearer',
        //     'expires_in' => auth()->factory()->getTTL() * 60,
        //     'user' => $user,
        // ]);

        return redirect()->away('http://localhost:5173/oauth/callback?' . http_build_query([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => json_encode([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                
            ])
        ]));
    }

    public function googleLoginUrl()
    {
        return response()->json([
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
        ]);
    }
}
