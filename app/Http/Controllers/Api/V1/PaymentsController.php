<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProcessCardPaymentRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class PaymentsController extends Controller
{
    /**
     * Handle card payment via Stripe
     */
    public function processCardPayment(ProcessCardPaymentRequest $request)
    {
        $validatedData = $request->validated();

        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $validatedData['amount'] * 100,
                'currency' => 'aed',
                'payment_method' => $validatedData['token'],
                'confirmation_method' => 'manual',
                'confirm' => true,
                'description' => 'Appointment Payment',
            ]);

            $cardDetails = $paymentIntent->charges->data[0]->payment_method_details->card ?? null;

            $payment = Payment::create([
                'appointment_id' => $validatedData['appointment_id'],
                'amount' => $validatedData['amount'],
                'payment_status' => 'completed',
                'payment_method' => 'stripe',
                'card_last4' => $cardDetails?->last4,
                'card_brand' => $cardDetails?->brand,
                'card_exp_month' => $cardDetails?->exp_month,
                'card_exp_year' => $cardDetails?->exp_year,
            ]);

            return response()->json([
                'message' => 'Payment successful.',
                'clientSecret' => $paymentIntent->client_secret,
                'payment_id' => $payment->id,
            ]);
        } catch (ApiErrorException $e) {
            return response()->json(['error' => 'Stripe error: ' . $e->getMessage()], 500);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Create payment intent for Apple Pay
     */
    public function createApplePayIntent(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.1',
            'appointment_id' => 'required|exists:appointments,id',
        ]);
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $validated['amount'] * 100,
                'currency' => 'aed',
                'payment_method_types' => ['card'], // Apple Pay will work under 'card'
            ]);

            $payment = Payment::create([
                'appointment_id' => $validated['appointment_id'],
                'amount' => $validated['amount'],
                'payment_status' => 'pending',
                'payment_method' => 'apple_pay',
            ]);

            return response()->json([
                'message' => 'Apple Pay intent created.',
                'clientSecret' => $paymentIntent->client_secret,
                'payment_id' => $payment->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to create Apple Pay intent: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update status of a payment
     */
    public function updatePaymentStatus(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'payment_status' => 'required|in:completed,failed,cancelled',
        ]);

        try {
            $payment = Payment::findOrFail($validated['payment_id']);
            $payment->update(['payment_status' => $validated['payment_status']]);

            return response()->json(['message' => 'Payment status updated successfully.']);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to update payment status: ' . $e->getMessage()], 500);
        }
    }
}
