<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function register(Request $r){
        $data = $r->validate([
            'name'=>'required|string|max:100',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8'
        ]);
        $user = User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>bcrypt($data['password'])
        ]);
        return response()->json(['user'=>$user], 201);
    }

    public function login(Request $r){
        $credentials = $r->validate(['email'=>'required|email', 'password'=>'required']);
        if(!Auth::attempt($credentials)) return response()->json(['message'=>'Invalid'], 401);
        $token = $r->user()->createToken('api')->plainTextToken;
        return response()->json(['token'=>$token]);
    }

    public function logout(Request $r){
        $r->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'ok']);
    }
}
