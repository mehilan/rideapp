<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{

    public function submit(Request $request)
    {

        // validate the phone number

        $request->validate([
            'phone' => 'required|numeric|min:10'
        ]);

        // find or create a user model

        $user = User::firstOrCreate([
            'phone' => $request->phone,
        ]);

        if(!$user){
            return response()->json([
                'message' => 'could not process a user with that phone number'
            ], 401);
        }

        // send the user a one-time use code

        $user->notify(new LoginNeedsVerification());

        // return back the response
        return response()->json([
            'message' => 'Notification send via sms',
            'login_code' => $user->login_code,
        ], 200);

    }

    public function verify(Request $request)
    {

        // validate the incoming request
        $request->validate([
            'phone' => 'required|numeric|min:10',
            'login_code' => 'required|numeric|between:111111, 999999'
        ]);

        // find the user
        $user  = User::where('phone', $request->phone)->where('login_code', $request->login_code)->first();

        // dd($user);

        // is the code provided the same one saved?
        // if so, return back an auth token

        if($user)
        {
            $user->update([
                'login_code' => null
            ]);
            return $user->createToken($request->login_code)->plainTextToken;
        }

        // if not, return back a message

        return response()->json([
            'message' => 'invalid verification code.'
        ], 401);

    }

    // public function otpGenerate()
    // {
    //     $otp = '';

    //     for($i=0; $i < 6; $i++){

    //         $num = rand(0, 9);

    //         $otp .= $num;
    //     }

    //     return $otp;
    // }
}
