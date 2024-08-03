<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;

use Validator;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required',
            'password'=>'required',
            'c_password'=>'required|same:password',
        ]); 

        if($validator->fails()) {
            return $this->sendError('validation error',$validator->errors());
        }

        $input = $request->all();
        $input['password']= bcrypt($input['password']);
        $user = User::create($input);
        $success['user'] =$user; 

        return $this->sendResponse($success,'User registered successfully');
    }

    public function login(Request $request){
        $credentials = request(['email','password']);
        if (!$token = JWTAuth::attempt($credentials)) {
            return $this->sendError('Unauthorised.',['error' => 'Unauthrised']);
        }
        
        $success =  $this->respondWithToken($token);
        return $this->sendResponse($success,'User logged in successfully');
    }

    protected function respondWithToken($token)
    {         
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function me()
    {
        return response()->json(JWTAuth::user());
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return response()->json(['token' => $token]);
    }
}

