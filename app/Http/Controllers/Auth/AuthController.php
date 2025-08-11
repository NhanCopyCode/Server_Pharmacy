<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        $refreshToken = Str::random(60);
        DB::table('refresh_tokens')->updateOrInsert([
            'user_id' => $user->id,
        ], [
            'token' => $refreshToken,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user,
        ]);
    }


    public function register(RegisterRequest $request)
    {
        $refreshToken = Str::random(60); 

        $user = User::create([
            'surname' => $request->surname,
            'name' => $request->name,
            'email' => $request->email,
            'phoneNumber' => $request->phoneNumber,
            'password' => bcrypt($request->password),
            'address' => $request->address,
            'roleId' => $request->roleId ?? 2,
            'refresh_token' => $refreshToken, 
        ]);

        $token = auth()->login($user);

        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user,
        ]);
    }


    public function me()
    {
        return response()->json(auth()->user());
    }


    public function logout(Request $request)
    {
        auth()->logout();

        DB::table('refresh_tokens')->where('user_id', auth()->id())->delete();

        return response()->json(['message' => 'Đăng xuất thành công']);
    }


    public function refresh(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        $row = DB::table('refresh_tokens')->where('token', $refreshToken)->first();

        if (!$row) {
            return response()->json(['message' => 'Refresh token không hợp lệ'], 401);
        }

        $user = User::find($row->user_id);
        $newAccessToken = auth()->login($user);

        return response()->json([
            'access_token' => $newAccessToken,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }


    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
