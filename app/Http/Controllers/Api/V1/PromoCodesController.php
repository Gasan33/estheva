<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PromoCodes;
use Illuminate\Http\Request;

class PromoCodesController extends Controller
{
    // Validate the promo code
    public function validatePromoCode(Request $request)
    {
        $promoCode = PromoCodes::where('code', $request->code)->first();

        if (!$promoCode) {
            return response()->json(['message' => 'Promo code not found'], 400);
        }

        if (!$promoCode->isValid()) {
            return response()->json(['message' => 'Promo code is invalid or expired'], 400);
        }

        return response()->json([
            'message' => 'Promo code is valid',
            'promo_code' => $promoCode
        ]);
    }

    // Apply the promo code to a treatment or total price
    public function applyPromoCode(Request $request)
    {
        $promoCode = PromoCodes::where('code', $request->code)->first();

        if (!$promoCode || !$promoCode->isValid()) {
            return response()->json(['message' => 'Invalid or expired promo code'], 400);
        }

        // Calculate the discount
        $originalPrice = $request->input('original_price');
        $discountedPrice = $originalPrice;

        if ($promoCode->discount_type === 'percentage') {
            $discountedPrice = $originalPrice - ($originalPrice * ($promoCode->discount_value / 100));
        } elseif ($promoCode->discount_type === 'fixed') {
            $discountedPrice = $originalPrice - $promoCode->discount_value;
        }

        // Prevent negative prices
        $discountedPrice = max($discountedPrice, 0);

        // Update usage count
        $promoCode->increment('usages');

        return response()->json([
            'original_price' => $originalPrice,
            'discounted_price' => $discountedPrice,
            'promo_code' => $promoCode->code
        ]);
    }
}
