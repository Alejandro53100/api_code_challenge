<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\PersonalAccessToken;


class AuthController extends Controller
{
    public function createUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
            [
                "name" => 'required',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'error' => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Usuario creado',
                'success' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }
    public function loginUser(Request $request)
    {
            try {
                // $validateUser = Validator::make($request->all(),
                // [
                //     'email' => 'required|email',
                //     'password' => 'required'
                // ]);

                // if($validateUser->fails()){
                //     return response()->json([
                //         'status' => false,
                //         'message' => 'validation error',
                //         'errors' => $validateUser->errors()
                //     ], 401);
                // }

                if(!Auth::attempt($request->only(['email', 'password']))){
                    return response()->json([
                        // 'status' => false,
                        'message' => 'Unauthenticated',
                    ], 401);
                }

                $user = User::where('email', $request->email)->first();

                return response()->json([
                    'status' => true,
                    'message' => 'User Logged In Successfully',
                    'token' => $user->createToken("API TOKEN")->plainTextToken
                ], 200);

            } catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'message' => $th->getMessage()
                ], 500);
            }
        }

        public function createToken($id)
        {
            return PersonalAccessToken::select('token')->where('tokenable_id','=',$id)->get();
        }
    }
