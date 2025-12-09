<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    // ✅ User Registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // password_confirmation required
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate token
        // $token = JWTAuth::fromUser($user);
        $token = $user->createToken('auth_token')->plainTextToken; // for sanctum


        return response()->json([
            'message' => 'Registration successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    // ✅ User Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Generate token
        // $token = JWTAuth::fromUser($user);
        $token = $user->createToken('auth_token')->plainTextToken; // for sanctum


        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);
    }


    // =======================
    // Web logout (Blade session)
    // =======================
    public function logout(Request $request)
    {
        try{
        Auth::logout();                       // Log out web session
        $request->session()->invalidate();    // Invalidate session
        $request->session()->regenerateToken(); // Regenerate CSRF token
        return response()->json(['message' => 'Logged out successfully']);
        }catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e) {

        }

        return response()->json(['message' => 'Failed to logout, token invalid'], 400);

        // return redirect('/'); // Redirect to home or login page
    }

    // =======================
    // API logout (Sanctum token)
    // =======================
    public function apiLogout(Request $request)
    {
        try {
            // Invalidate the JWT token
            JWTAuth::parseToken()->invalidate();

            return response()->json(['message' => 'Logged out successfully']);
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Failed to logout, token invalid'], 400);
        }
    }
    //  public function apiLogout(Request $request)
    // {
    //     $token = $request->user()->currentAccessToken();

    //     if ($token) {
    //         $token->delete(); // Deletes current API token
    //         return response()->json(['message' => 'Logged out successfully']);
    //     }

    //     return response()->json(['message' => 'No token found'], 400);
    // }

    public function refresh(Request $request)
    {
        try {
            $new_token = JWTAuth::parseToken()->refresh();

            return response()->json([
                'message' => 'Token refreshed successfully',
                'access_token' => $new_token,
                'token_type' => 'Bearer'
            ]);
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Failed to refresh token', 'error' => $e->getMessage()], 401);
        }
    }
}







    // public function Logout(Request $request)
    // {
    //     $request->user()->currentAccessToken()->delete(); // Deletes only current token
    //     return response()->json(['message' => 'Logged out successfully']);
    // }


    //     public function logout(Request $request)
    // {
    //     try {
    //         JWTAuth::parseToken()->invalidate();
    //         return response()->json(['msg' => "success"]);
    //     } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
    //         return response()->json(['msg' => $e->getMessage()]);
    //     }
    // }



    // // // ✅ Logout (optional)
    // // public function logout(Request $request)
    // // {
    // //     $request->user()->currentAccessToken()->delete();

    // //     return response()->json([
    // //         'message' => 'Logged out successfully'
    // //     ]);
    // // }
