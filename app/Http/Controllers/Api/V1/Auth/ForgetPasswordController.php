<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ForgetPasswordRequest;
use App\Mail\ForgetPasswordMail;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Mail;

class ForgetPasswordController extends Controller
{
    public function forgetPassword(ForgetPasswordRequest $forgetPasswordRequest)
    {
        $email = $forgetPasswordRequest->email;
        if (User::where('email', $email)->doesntExist()) {
            return response([
                "message" => "Email Invalid"
            ], 401);
        }

        $token = rand(10, 100000);

        try {

            FacadesDB::table('password_reset_tokens')->insert(
                [
                    'email' => $email,
                    "token" => $token
                ]
            );

            Mail::to($email)->send(new ForgetPasswordMail($token));

            return response(
                ["message" => "Reset Password Mail send to your email."],
                200
            );

        } catch (Exception $exception) {
            return response(
                ["message" => $exception->getMessage()],
                400
            );
        }
    }
}
