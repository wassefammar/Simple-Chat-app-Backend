<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function register(Request $request){
        $attrs= $request->validate([
            'username' =>'required|string',
            'email'=>'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        $user= User::create([
            'username'=>$attrs['username'],
            'email'=>$attrs['email'],
            'password'=>bcrypt($attrs['password'])
        ]);

        return response([
            'user'=>$user,
            'token'=>$user->createToken('secret')->plainTextToken
        ],200);
    }

    public function login(Request $request){
        $attrs= $request->validate([
            'email'=>'required|email',
            'password' => 'required|min:6'
        ]);
        if(!Auth::attempt($attrs)){
            return response([
             'message'=>'Invalid Credentials',
            ], 403);
        }
       //returns user & token in response
        return response([
            'user'=>auth()->user(),
            'token'=>auth()->user()->createToken('secret')->plainTextToken
        ],200);
    }

    public function logout(Request $request){
        auth('sanctum')->user()->tokens()->delete();
        return response()->json([
            'message'=> 'logout succes'
        ],200);
    }

    //get user details 
    public function usery(){
        $user=auth('sanctum')->user();
        return response([
            'user'=>$user
        ], 200);
    }


    public function update(Request $request){
        $attrs= $request->validate([
            'username'=>'required'
        ]); 
        auth('sanctum')->user()->update([
            'username'=>$attrs['username'],
        ]);
        return response()->json([
            'message'=> 'user updated',
            'user'=>auth('sanctum')->user()
        ],200);
    }
}
