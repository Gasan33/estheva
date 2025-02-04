<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AvailabilityResource;
use App\Models\Availability;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    /**
     * Display a listing of the schedules.
     */
    public function index()
    {
        // Retrieve all schedules with the related 'doctor' relationship
        $availability = Availability::with('doctor')->get();

        // Return the schedules as a resource collection
        return $this->api()->success($availability);
        // return AvailabilityResource::collection($availability);
    }

    /**
     * Store a newly created schedule in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'day_of_week' => 'required|string|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time', // Ensure end_time is after start_time
        ]);

        // Create the new schedule
        $availability = Availability::create($validated);

        // Return the newly created schedule as a resource
        return new AvailabilityResource($availability);
    }

    /**
     * Display the specified schedule.
     */
    public function show($id)
    {
        // Find the schedule by ID, including the related 'doctor' model
        $availability = Availability::with('doctor')->findOrFail($id);

        // Return the schedule as a resource
        return new AvailabilityResource($availability);
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'doctor_id' => 'sometimes|exists:doctors,id',
            'day_of_week' => 'sometimes|string|max:20',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
        ]);

        // Find the schedule by ID
        $availability = Availability::findOrFail($id);

        // Update the schedule with the validated data
        $availability->update($validated);

        // Return the updated schedule as a resource
        return new AvailabilityResource($availability);
    }

    /**
     * Remove the specified schedule from storage.
     */
    public function destroy($id)
    {
        // Find the schedule by ID
        $availability = Availability::findOrFail($id);

        // Delete the schedule
        $availability->delete();

        // Return a success message
        return response()->json(['message' => 'Schedule deleted successfully.']);
    }
}



