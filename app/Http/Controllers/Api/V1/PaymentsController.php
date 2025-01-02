<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Token;
use Stripe\Exception\ApiErrorException;

class PaymentsController extends Controller
{
    public function processPayment(Request $request)
    {
        // Validate token and amount
        $request->validate([
            'token' => 'required|string',
            'amount' => 'required|numeric|min:0.1',
        ]);

        // Set Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Create the charge using the token
            $charge = Charge::create([
                'amount' => $request->amount * 100,  // Amount in cents
                'currency' => 'usd',
                'description' => 'Test Payment',
                'source' => $request->token,  // Use the token sent from the client
            ]);

            return response()->json(['success' => 'Payment successful!', 'data' => $charge]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
