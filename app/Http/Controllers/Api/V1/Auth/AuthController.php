<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RegisterUserRequest;
use App\Http\Requests\Api\V1\UserVerifyRequest;
use App\Models\User;
use App\Services\ApiResponse;
use App\Services\SMSService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            if (Auth::attempt($request->only("email", "password"))) {
                $user = Auth::user();
                if ($user) {
                    $accessToken = $request->user()->createToken('authToken')->accessToken;
                    return $this->api()->success([
                        "user" => $user,
                        "access_token" => $accessToken
                    ]);
                }
            }

        } catch (Exception $exception) {
            return response(
                ["message" => $exception->getMessage()],
                400
            );
        }

        return response(
            ["message" => "Invalid Email Or Password"],
            401
        );
    }

    public function register(RegisterUserRequest $request)
    {
        try {
            if (User::where('email', $request->email)->exists()) {
                return response([
                    'message' => 'Email already exists or invalid data.',

                ], );
            }
            if (User::where('phone_number', $request->phone_number)->exists()) {
                return response([
                    'message' => 'Phone number already exists please try again.',

                ], );
            }
            $validatedData = $request->validated();

            // Create the user
            $user = User::create([
                'name' => $validatedData['name'] ?? $request->first_name . ' ' . $request->last_name,
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'phone_number' => preg_replace_callback('/^(0|)?(\d{9})$/', function ($matches) {
                    return '+971' . $matches[2];
                }, ltrim($validatedData['phone_number'], '+')),
                'password' => Hash::make($validatedData['password']),
                'device_token' => $validatedData['device_token'] ?? null,
            ]);


            SMSService::sendOtp($user);

            $accessToken = $user->createToken('authToken')->accessToken;

            return $this->api()->created(['user' => $user, 'access_token' => $accessToken]);
        } catch (Exception $exception) {
            return response(
                ["message" => $exception->getMessage()],
                400
            );
        }

    }



    public function generateOpt(Request $request)
    {

        $user = User::where('phone_number', $request->phone_number)->first();
        // dd($user);
        $verificationCode = SMSService::sendOtp($user);
        // dd($verificationCode);
        return $this->api()->success($verificationCode);
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

    public function verify(UserVerifyRequest $request)
    {
        if (!$request->isValidOtp()) {
            return $this->api()->validation('invalid otp');
        }
        $user = $request->user();

        if (Auth::attempt($request->only("email", "password"))) {
            $user = Auth::user();
            if ($user) {
                $accessToken = $request->user()->createToken('authToken')->accessToken;
                return response([
                    "message" => "Successfully Login",
                    "user" => $user,
                    "access_token" => $accessToken
                ], 200);
            }
        }

        return $this->api()->created($user);
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Get the user's current token and revoke it
            $token = $user->token(); // Correct method for Passport
            $token->revoke(); // Revoke the token

            return response()->json([
                'message' => 'Logout successful'
            ], 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

}