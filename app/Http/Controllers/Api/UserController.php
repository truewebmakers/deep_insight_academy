<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * User Registration
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|max:255',
            'phoneno' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }
        DB::beginTransaction();
        try {
            $user = user::where('email', $request->email)->first();
            if ($user) {
                return response()->json([
                    'status_code' => 400,
                    'message' => 'User already registered'
                ], 200);
            }
            $user = new user();
            $user->email = trim($request->email);
            $user->name = $request->name;
            $user->password = Hash::make($request->password);
            $user->phoneno = $request->phoneno;
            $user->save();
            DB::commit();
            $token = $user->createToken('UserToken', ['user'])->accessToken; //specify scope name
            $user->makeHidden('password');
            return response()->json([
                'status_code' => 200,
                'data' => $user,
                'token' => $token,
                'message' => 'User Registered Successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to register user.'
            ], 500);
        }
    }

    /**
     * User Login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => 400,
                'message' => $validator->messages()
            ], 400);
        }
        try {
            $user = user::where('email', $request->email)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('UserToken', ['user'])->accessToken; //specify scope name
                    $user->makeHidden('password');
                    return response()->json([
                        'status_code' => 200,
                        'data' => $user,
                        'token' => $token,
                        'message' => 'Login Successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'status_code' => 401,
                        'message' => 'Invalid password',
                    ], 200);
                }
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Invalid Credentials',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Failed to login user.'
            ], 500);
        }
    }
}
