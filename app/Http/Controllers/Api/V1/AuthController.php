<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserAuthRequest;
use App\Http\Requests\Api\V1\UserLoginRequest;
use App\Http\Requests\Api\V1\UserRegisterRequest;
use App\Http\Requests\Api\V1\UserVerifyRequest;
use App\Models\User;
use App\Services\ApiResponse;
use App\Services\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        if (!User::where('email', $request->email)->first()) {
            return api()->notFound("No account found with this email address.");

        } elseif (!$token) {
            return api()->unauth();
        } else {
            $user = Auth::user();
            return api()->success(
                [
                    'user' => $user,
                    'authorisation' => [
                        'type' => 'bearer',
                        'token' => $token,

                    ]
                ],
                "User Login Successfully"
            );
        }
    }
    public function register(Request $request)
    {
        if (User::where('email', $request->email)->exists()) {
            return api()->validation([
                'message' => 'Email already exists or invalid data.',

            ], );
        }
        if (User::where('phone_number', $request->phone_number)->exists()) {
            return api()->validation([
                'message' => 'Phone number already exists please try again.',

            ], );
        }

        // Create the user
        $user = User::create([
            'name' => $request->name != null ? $request->name : $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name != NULL ? $request->first_name : Str::before($request->name, ' '),
            'last_name' => $request->last_name != NULL ? $request->last_name : Str::after($request->name, ' '),
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'device_token' => $request->device_token,
            'password' => Hash::make(
                $request->password
            ),
        ]);


        SMSService::sendOtp($user);
        // $this->generateOpt($request->phone_number);

        return api()->success($user);
    }

    public function generateOpt(Request $request)
    {

        $user = User::where('phone_number', $request->phone_number)->first();
        // dd($user);
        $verificationCode = SMSService::sendOtp($user);
        // dd($verificationCode);
        return api()->success($verificationCode);
    }

    public function verify(UserVerifyRequest $request)
    {
        if (!$request->isValidOtp()) {
            return api()->validation('invalid otp');
        }
        $user = $request->user();

        $token = JWTAuth::fromUser($user);

        $user['token'] = $token;

        return api()->created($user);
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent successfully.']);
        }

        return response()->json(['message' => 'Unable to send reset link.'], 400);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully.']);
        }

        return response()->json(['message' => 'Invalid token or email.'], 400);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully']);
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return api()->success('logout success');
    }


}

if (!function_exists('api')) {

    function api()
    {
        return new ApiResponse();
    }
}
