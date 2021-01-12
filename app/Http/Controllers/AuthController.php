<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

use Tymon\JWTAuth\Contracts\JWTSubject;

class AuthController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api',
        ['except'=> ['login','register']]
    );
    }


    public function login(Request $request){

        $credentials = $request->only("email", "password");

        if($token = \Auth::guard('api')->attempt($credentials)){
            $userInfo=auth('api')->user();
            
            //return $this->respondWithToken($token);
            return response()->json(['token' => $token , 'id' => $userInfo->id]);

        }
        return response()->json([
            'status' => 'error',
            'error' => 'Invalid username or password'
        ]);
    }

    public function register(Request $request){
        $record = new User;
        $record->name = $request->name;
        $record->email = $request->email;
        $record->password = Hash::make($request->password);

        $record->save();
        return response()->json([
            'status' => true,
            'message' => 'User Created'
        ]);
    }


    public function guard(){
        return \Auth::guard('api');
    }
    
    public function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory->getTTL()*60,
           
        ]);
    }
}
