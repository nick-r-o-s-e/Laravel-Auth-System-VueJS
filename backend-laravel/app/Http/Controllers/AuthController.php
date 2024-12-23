<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validFields = $request->validate(([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'country' => 'required|string',
            'language' => 'required|string'
        ]));

        $user = User::create([
            'name' => $validFields['name'],
            'email' => $validFields['email'],
            'password' => Hash::make($validFields['password']),
            'country' => $validFields['country'],
            'language' => $validFields['language']
        ]);

        if ($user) {
            $token = $user->createToken($request->email);

            return response()->json([
                'user' => $user,
                'token' => $token->plainTextToken
            ], 201);
        } else {
            return response()->json(
                ['message' => 'Something went wrong while registration'],
                500
            );
        }
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate(([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]));

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                ['errors' => [
                    'email' => ['The provided credentials are incorrect']
                ]],
                401
            );
        }

        $token = $user->createToken($request->email);

        return response()->json([
            'message' => 'Login successful',
            'token_type' => 'Bearer',
            'token' => $token->plainTextToken
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = User::where('id', $request->user()->id)->first();

        if ($user) {
            $user->tokens()->delete();
            return response()->json(
                [
                    'message' => 'Logget Out Successfully',
                    'data' => $request->user()
                ],
                200
            );
        } else {
            return response()->json(
                ['message' => 'Use Not Found'],
                404
            );
        }

        return ['message' => 'You are logged out'];
    }

    public function profile(Request $request)
    {
        if ($request->user()) {
            return response()->json(
                [
                    'message' => 'Profile Data Fetched',
                    'data' => $request->user()
                ],
                200
            );
        } else {
            return response()->json(
                ['message' => 'Not Auathenticated'],
                401
            );
        }
    }
}
