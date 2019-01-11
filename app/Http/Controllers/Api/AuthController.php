<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Validator;

class AuthController extends Controller
{
    //
    
    public function register(Request $request)
    {
    $user = Validator::make($request->all(), ([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required',
        ]));
    	
    	if ($user->fails()) {
					return response()->json(['error' => $user->errors()], 401);
				}
				
    	$user = User::create([
    		'name' => $request->name,
    		'email' => $request->email,
    		'password' => bcrypt($request->password)
    	]);
    	
    	
    	$token = auth('api')->login($user);
    	return $this->respondWithToken($token);
    }
    
    public function login(Request $request)
    {
    	$credentials = $request->only(['email', 'password']);
    	
    	if(!$token = auth('api')->attempt($credentials)) {
    		return response()->json(['error' => 'Unauthorised'], 401);
    		}
    		
    		return $this->respondWithToken($token);
    }
    
    protected function respondWithToken($token)
    {
    	return response()->json([
    	'access-token' => $token,
    	'token_type' => 'bearer',
    	'expires_in' => auth('api')->factory()->getTTL() * 60
    	]);
    }
}
