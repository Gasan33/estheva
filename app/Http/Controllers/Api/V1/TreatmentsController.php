<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TreatmentRequest;
use App\Http\Resources\Api\V1\TimeSlotResource;
use App\Http\Resources\Api\V1\TreatmentsResource;
use App\Models\Availability;
use App\Models\TimeSlot;
use App\Models\Treatment;
use App\Services\ApiResponse;
use App\Services\TimeSlotService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class TreatmentsController extends Controller
{
    public function index()
    {
        try {
            $treatments = Treatment::with(['category', 'doctors', 'timeSlots', 'reviews'])->get();
            return $this->api()->success(TreatmentsResource::collection($treatments));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function store(TreatmentRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $treatment = Treatment::create($validatedData);

            // Attach doctors
            $treatment->doctors()->sync($request->doctor_id ?? []);

            // Generate time slots if duration is provided
            if ($treatment->duration && !empty($request->doctor_id)) {
                foreach ($request->doctor_id as $doctorId) {
                    $availabilities = Availability::where('doctor_id', $doctorId)->get();

                    foreach ($availabilities as $availability) {
                        TimeSlotService::generateSlots(
                            $doctorId,
                            $treatment->id,
                            Carbon::now()->toDateString(),
                            $availability->start_time,
                            $availability->end_time,
                            $treatment->duration
                        );
                    }
                }
            }

            return $this->api()->created(new TreatmentsResource($treatment->load(['category', 'doctors', 'reviews'])), "Treatment Created Successfully.");
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }


    public function show($id)
    {
        try {
            $treatment = Treatment::with(['category', 'doctors', 'reviews'])->findOrFail($id);
            return $this->api()->success(new TreatmentsResource($treatment));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function update(TreatmentRequest $request, $id)
    {
        try {
            $treatment = Treatment::findOrFail($id);
            $validatedData = $request->validated();

            // Update treatment
            $treatment->update($validatedData);

            // Sync doctors (remove all if none provided)
            $treatment->doctors()->sync($request->doctor_id ?? []);

            return $this->api()->success(new TreatmentsResource($treatment->load(['category', 'doctors', 'reviews'])), "Treatment Updated Successfully.");
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $treatment = Treatment::findOrFail($id);
            $treatment->delete();
            return $this->api()->success([], 'Treatment Deleted Successfully');
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function getDiscountedPrice($id)
    {
        try {
            $treatment = Treatment::findOrFail($id);
            return response()->json(['discounted_price' => $treatment->getDiscountedPrice()]);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function topRated()
    {
        try {
            // Retrieve treatments with their reviews, ensuring we calculate average rating
            $treatments = Treatment::with(['category', 'doctors', 'reviews'])
                ->get()
                ->filter(function ($treatment) {
                    // Ensure that the treatment has at least one review and calculate the average rating
                    return $treatment->reviews->isNotEmpty() && $treatment->reviews->avg('rating') !== null;
                })
                ->sortByDesc(function ($treatment) {
                    // Sort treatments by average rating
                    return $treatment->reviews->avg('rating');
                });

            // If no treatments were found, return a message indicating no top-rated treatments
            if ($treatments->isEmpty()) {
                return response()->json(['message' => 'No top-rated treatments found.'], 404);
            }

            return $this->api()->success(TreatmentsResource::collection($treatments));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function homeBased()
    {
        try {
            $treatments = Treatment::with(['category', 'doctors', 'timeSlots', 'reviews'])
                ->where('home_based', true)
                ->get();

            return $this->api()->success(TreatmentsResource::collection($treatments));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    public function getTreatmentByCategory($category_id)
    {
        try {
            $treatments = Treatment::with(['category', 'doctors', 'timeSlots', 'reviews'])
                ->where('category_id', $category_id)
                ->get();

            return $this->api()->success(TreatmentsResource::collection($treatments));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }
}
