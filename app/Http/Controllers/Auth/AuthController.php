<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    // public function refresh(Request $request)
    // {
    //     $refreshToken = $request->cookie('refresh_token');

    //     if (!$refreshToken) {
    //         return response()->json(['error' => 'Không tồn tại refresh token'], 401);
    //     }

    //     // Tìm user theo refresh_token (đã hash)
    //     $user = User::where('refresh_token', hash('sha256', $refreshToken))->first();

    //     if (!$user) {
    //         return response()->json(['error' => 'Refresh token không hợp lệ'], 401);
    //     }

    //     // Đăng nhập user
    //     $newAccessToken = auth()->login($user);

    //     // Rotate refresh token
    //     $newRefreshToken = Str::random(60);
    //     $user->refresh_token = hash('sha256', $newRefreshToken);
    //     $user->save();

    //     return response()
    //         ->json([
    //             'access_token' => $newAccessToken,
    //             'token_type' => 'bearer',
    //             'expires_in' => auth()->factory()->getTTL() * 60,
    //         ])
    //         ->cookie('refresh_token', $newRefreshToken, 60 * 24 * 7, '/', null, false, true, false, 'Lax');


    // }


    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (!$token = auth()->attempt($credentials)) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     $user = auth()->user();
    //     $refreshToken = Str::random(60);
    //     $user->refresh_token = hash('sha256', $refreshToken);
    //     $user->save();

    //     return response()
    //         ->json([
    //             'user' => $user,
    //             'access_token' => $token,
    //             'token_type' => 'bearer',
    //             'expires_in' => auth()->factory()->getTTL() * 60,
    //         ])
    //         ->cookie('refresh_token', $refreshToken, 60 * 24 * 7, '/', null, false, true, false, 'Lax');
    // }

    // protected function respondWithToken($token)
    // {
    //     $refreshToken = Str::random(60);
    //     $user = auth()->user();
    //     $user->refresh_token = hash('sha256', $refreshToken);
    //     $user->save();

    //     return response()
    //         ->json([
    //             'access_token' => $token,
    //             'token_type' => 'bearer',
    //             'expires_in' => auth()->factory()->getTTL() * 60,
    //         ])
    //         ->cookie('refresh_token', $refreshToken, 60 * 24 * 7, null, null, true, true, false, 'Strict');
    // }


    // public function me()
    // {
    //     return response()->json(auth()->user());
    // }

    // public function logout(Request $request)
    // {
    //     $user = auth()->user();
    //     if ($user) {
    //         $user->refresh_token = null;
    //         $user->save();
    //     }

    //     auth()->logout();

    //     return response()
    //         ->json(['message' => 'Successfully logged out'])
    //         ->cookie('refresh_token', '', -1); 
    // }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user
        ]);
    }


    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function refresh()
    // {
    //     return $this->respondWithToken(auth()->refresh());
    // }
    public function refresh(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['message' => 'Không tồn tại token'], 400);
            }
            $newToken = JWTAuth::setToken($token)->refresh();



            return response()->json([
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token expired and cannot be refreshed'], 401);
        }
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
