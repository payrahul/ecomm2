<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Str;
use Validator;
use App\Traits\GetArray;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
class AuthController extends BaseController
{
    use GetArray;
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'unique:users|required',
            'password'=>'required|min:6',
            'c_password'=>'required|same:password',
            'role_id'=>'required',
            'phone_number'=>'required'
        ]); 

        if($validator->fails()) {
            return $this->sendError('validation error',$validator->errors());
        }
        $input = $request->all();
        $input['password']= bcrypt($input['password']);
        $input['uuid']= (string) Str::uuid();;
        $user = User::create($input);
        $success['user'] =$user; 
        return $this->sendResponse($success,'User registered successfully');
    }

    public function deleteUser(Request $request)
    {
        $validator = Validator::make(request()->all(),[
            'uuid'=>'required|uuid'
        ]);
        if($validator->fails()) {
            return $this->sendError('validation error',$validator->errors());
        }
        $user = User::where('uuid',request()->uuid)->first();
        if(!empty($user)) {
            $user->delete();
            return $this->sendResponse(null,'User deleted successfully');
        }else{
            return $this->sendError('uuid error',['Check uuid']);
        }
    }

    public function updateUser(Request $request)
    {
        Log::info('Storing user data', ['data' => $request->all()]);
        Log::channel('custom')->info('This message should appear in the custom.log file.');
        $result = $this->getRoles();
        $keys = [];
        foreach($result as $key => $value){
            $keys[] = $key;
        }
        
        $validator = Validator::make($request->all(),[
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($request->uuid, 'uuid'),
            ],
            'name'=>'required',
            'phone_number'=>'required',
            'role_id'=>[
                'required',
                Rule::in($keys),
            ],
            'uuid'=>'required|uuid'
        ]);
        if($validator->fails()) {
            return $this->sendError('validation error',$validator->errors());
        }
        $input['name']= $request->name;
        $input['email']= $request->email;
        $input['phone_number']= $request->phone_number;
        $input['role_id']= $request->role_id;
        $user = User::where('uuid',$request->uuid)->first();
        if(!empty($user)){
            $user->Update($input);
            return $this->sendResponse(true,'Data updated successfully');
        }else{
            return $this->sendError('Error','check given details');
        }
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

