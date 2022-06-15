<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users',
                'password' => 'required'
            ]);

            if ($validator->fails()) return response()->json(Arr::flatten($validator->errors()->messages()), 400);;

            $user = User::where('email', $request->email)->first();

            if(!Hash::check($request->password, $user->password)) return response()->json("Password did not match", 400);

            $token = $user->createToken('my-stack-questions')->accessToken;
            var_dump($token);

            return response()->json(['token' => $token], 200);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json($ex->getMessage(), 500);
        }
    }
}
