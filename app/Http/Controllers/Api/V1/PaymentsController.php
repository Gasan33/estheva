<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProcessCardPaymentRequest;
use App\Http\Requests\Api\V1\ProcessCashPaymentRequest;
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
        try {
            $validatedData = $request->validated();
            Stripe::setApiKey(config('services.stripe.secret'));
            $paymentIntent = PaymentIntent::create([
                'amount' => $validatedData['amount'] * 100,
                'currency' => 'aed',
                // 'payment_method_types' => ['card'],
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ]);

            $details = $paymentIntent->charges->data[0]->payment_method_details->card ?? null;

            $payment = Payment::create([
                'appointment_id' => $validatedData['appointment_id'],
                'amount' => $validatedData['amount'],
                'payment_status' => 'paid',
                'payment_method' => 'credit_card',
                'card_last4' => $details?->last4,
                'card_brand' => $details?->brand,
                'card_exp_month' => $details?->exp_month,
                'card_exp_year' => $details?->exp_year,
            ]);

            return response()->json([
                'message' => 'Payment successful',
                'clientSecret' => $paymentIntent->client_secret,
                'payment_id' => $payment->id,
            ]);
        } catch (ApiErrorException $e) {
            return response()->json(['error' => 'Stripe error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
    public function processCashPayment(ProcessCashPaymentRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $payment = Payment::create([
                'appointment_id' => $validatedData['appointment_id'],
                'amount' => $validatedData['amount'],
                'payment_status' => 'pending',
                'payment_method' => 'cash',
            ]);

            return response()->json([
                'message' => 'Payment successful',
                'payment_id' => $payment->id,
            ]);
        } catch (ApiErrorException $e) {
            return response()->json(['error' => 'Stripe error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Create payment intent for Apple Pay
     */
    public function createApplePayPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.1',
            'appointment_id' => 'required|exists:appointments,id',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100,
                'currency' => 'aed',
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never', // Optional if you want to disable redirect-based methods
                ],
            ]);

            Payment::create([
                'appointment_id' => $request->appointment_id,
                'amount' => $request->amount,
                'payment_status' => 'pending',
                'payment_method' => 'apple_pay', // This might be more general like 'stripe' if method isn’t known yet
            ]);

            return response()->json([
                'message' => 'Apple Pay intent created',
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create Apple Pay intent: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Update status of a payment
     */
    public function updatePaymentStatus(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'payment_status' => 'required|in:completed,failed,cancelled',
        ]);

        try {
            $payment = Payment::findOrFail($request->payment_id);
            $payment->payment_status = $request->payment_status;
            $payment->save();

            return response()->json(['message' => 'Payment status updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update payment status: ' . $e->getMessage()], 500);
        }
    }
}
