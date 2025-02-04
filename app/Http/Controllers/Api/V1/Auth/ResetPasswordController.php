<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function resetpassword(ResetPasswordRequest $resetPasswordRequest)
    {
        $email = $resetPasswordRequest->email;
        $token = $resetPasswordRequest->token;
        $password = Hash::make($resetPasswordRequest->password);
        $emailcheck = DB::table('password_reset_tokens')->where('email', $email)->first();
        $pincheck = DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$emailcheck) {
            return response(['message' => "Email Not Found"], 401);
        }

        if (!$pincheck) {
            return response(['message' => "Pin Code Invalid"], 401);
        }

        DB::table('users')->where('email', $email)->update(['password' => $password]);
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return response([
            'message' => 'Password Change Successfully'
        ], 200);
    }
}
