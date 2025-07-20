<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        $user = User::where('email', $request->email)->first();
        
        if(!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        } else if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Incorrect password',
            ], 401);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->makeHidden('password'),
            'token' => $token,
        ], 200);
    }

    public function dashboard(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json([
                'message' => 'Welcome to the dashboard',
                'user' => $user,
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'error' => 'Token is expired',
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'error' => 'Token is invalid',
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {   
            return response()->json([
                'error' => 'Token is required',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token is invalid',
            ], 401);

        }
    }

    public function logout(Request $request) {
        try {
            JWTAuth::parseToken()->invalidate();
            return response()->json(['message' => 'Logout successful'], 200);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['error' => 'Token is expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token is required'], 401);
        }
    }
    
}
