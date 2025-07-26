<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            "confirm_password" => "required|same:password",
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                "data" => $validator->errors()->all()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $response = [];
        $response['token'] = $user->createToken('authToken')->accessToken;
        $response['name'] = $user->name;
        $response['email'] = $user->email;

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            "data" => $response
        ], 200);
    }

    public function login(Request $request) {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            $response = [];
            $response['token'] = $user->createToken('authToken')->accessToken;
            $response['name'] = $user->name;
            $response['email'] = $user->email;

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully',
                "data" => $response
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials',
            "data" => []
        ], 401);
    }
}