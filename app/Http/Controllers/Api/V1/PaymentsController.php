<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
class PaymentsController extends Controller
{
    public function processPayment(Request $request)
    {
        // Validate token and amount
        $request->validate([
            'amount' => 'required|numeric|min:0.1',
        ]);

        // Set Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Create a PaymentIntent with the amount and currency
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100,  // Amount in cents
                'currency' => 'usd',
                'description' => 'Test Payment',
            ]);

            return response()->json(['clientSecret' => $paymentIntent->client_secret]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createApplePayPayment(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:0.1']);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100,
                'currency' => 'usd',
                'payment_method_types' => ['card'],
            ]);

            return response()->json(['clientSecret' => $paymentIntent->client_secret]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
