<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
// use Illuminate\Support\Facades\Auth; 
use App\Models\User;
// use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        Validator::make($request->all(), [
            'email' => ['required'],
            'password'=> ['required'],
        ]);

        //$isAdmin = User::where('email',$request->email)->first();

        if(!$token = JWTAuth::attempt($request->only('email','password')))
        {
            return response(['error' => 'Incorrect email or password'], 401);
        }

        //Session::put('id', $request->id);

        $response = [
            'message' => 'Login was successful',
            'email' => $request->email,
            'token' => $token
        ];

        return response()->json($response);
    }
}
