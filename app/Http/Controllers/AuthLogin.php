<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthLogin extends Controller
{
    public function signup(Request $request) {
        $valid = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:users,name',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($valid->fails()) {
            return response()->json($valid->errors(),422);
        }

        $pengguna = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin'
        ]);
        return response()->json([
            'status' => 'berhasil',
            'token' => $pengguna->createToken('signup')->plainTextToken
        ]);
    }

    public function login(Request $request) {
        $valid = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($valid->fails()) {
            return response()->json($valid->errors(),422);
        } elseif (!Auth::attempt($request->all())) {
            return response()->json([
                'status' => 'gagal',
                'message' => 'password atau email anda salah'
            ],401);
        }
        $user = User::where('email', $request->email)->first();
        $user->tokens()->delete();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'token' => $user->createToken('login')->plainTextToken
        ]);
    }

    public function signout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'berhasil'
        ],204);
    }
}
