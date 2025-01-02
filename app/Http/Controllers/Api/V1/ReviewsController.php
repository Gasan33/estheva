<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ReviewResource;
use App\Models\Review;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class ReviewsController extends Controller
{
    /**
     * Display a listing of the reviews.
     */
    public function index()
    {
        // Get all reviews, with related patient, doctor, and service
        $reviews = Review::with(['patient', 'doctor', 'service'])->get();

        // Return the collection as a response using the ReviewResource
        return api()->success($reviews);
        // return ReviewResource::collection($reviews);
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request)
    {

        $review = Review::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'service_id' => $request->service_id,
            'rating' => $request->rating,
            'review_text' => $request->review_text,

        ]);
        return api()->created($review, "Review submited successfully.");

    }

    /**
     * Display the specified review.
     */
    public function show($id)
    {
        // Find the review by ID, including relationships
        $review = Review::with(['patient', 'doctor', 'service'])->find($id);
        if (!$review) {
            return api()->notFound('Review not Found');
        }

        return api()->success($review);
        // Return the review as a resource
    }

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'patient_id' => 'sometimes|exists:users,id',
            'doctor_id' => 'sometimes|exists:doctors,id',
            'service_id' => 'sometimes|exists:services,id',
            'rating' => 'sometimes|integer|between:1,5',
            'review_text' => 'sometimes|string',
        ]);

        // Find the review to update
        $review = Review::find($id);
        if (!$review) {
            return api()->notFound('Review not Found');
        }
        // Update the review with validated data
        $review->update($validated);

        // Return the updated review as a resource
        return api()->success($review, "Review Successfully Updated.");
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy($id)
    {
        // Find the review to delete
        $review = Review::find($id);
        if (!$review) {
            return api()->notFound('Review not Found');
        }

        // Delete the review
        $review->delete();

        // Return a success message
        return response()->json(['message' => 'Review deleted successfully.']);
    }
}


if (!function_exists('api')) {

    function api()
    {
        return new ApiResponse();
    }
}

