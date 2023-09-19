<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User
     */

     public function createUser(Request $request){
        try {
            $validateUser = Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            if($validateUser-> fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'password' =>Hash::make( $request->password),
            ]);

            return response()-> json([
                'status' => true,
                'message' => 'User created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ],200);
            
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage()
            ],500);
            
        }
     }

     /**
      * Login the User
      * @param Request $request
      * @return User
      */

      public function loginUser(Request $request){
        try {
            $validateUser = Validator::make($request->all(),[
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message'=>'Validation Error',
                    'error'=>$validateUser-> errors()
                ],401);
            }

            if(!Auth::attempt($request->only(['email','password']))){
                return response()->json([
                    'status'=> false,
                    'message'=> 'Email or password does not exists'
                ],401);
            }

            $user = User::where('email',$request->email)->first();

            return response()->json([
                'status'=> true,
                'message'=> 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage()
            ],500);
        }
      }
}
