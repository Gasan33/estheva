<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TimeSlotResource;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\services;
use App\Models\TimeSlot;
use App\Services\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimeSlotsController extends Controller
{
    /**
     * Display a listing of the availabilities.
     */
    public function index()
    {
        // Retrieve all availabilities, including the related 'doctor' and 'service' models
        $timeSlot = TimeSlot::with(['doctor', 'service'])->get();

        // Return the availabilities as a resource collection
        return TimeSlotResource::collection($timeSlot);
    }

    /**
     * Store a newly created availability in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time', // Ensure end_time is after start_time
            'is_available' => 'required|boolean',
        ]);

        // Create a new availability record
        $timeSlot = TimeSlot::create($validated);

        // Return the newly created availability as a resource
        return new TimeSlotResource($timeSlot);
    }

    /**
     * Display the specified availability.
     */
    public function show($id)
    {
        // Find the availability by ID, including the related 'doctor' and 'service'
        $timeSlot = TimeSlot::with(['doctor', 'service'])->findOrFail($id);

        // Return the availability as a resource
        return new TimeSlotResource($timeSlot);
    }

    /**
     * Update the specified availability in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'doctor_id' => 'sometimes|exists:doctors,id',
            'service_id' => 'sometimes|exists:services,id',
            'date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time', // Ensure end_time is after start_time
            'is_available' => 'sometimes|boolean',
        ]);

        // Find the availability by ID
        $timeSlot = TimeSlot::findOrFail($id);

        // Update the availability record with the validated data
        $timeSlot->update($validated);

        // Return the updated availability as a resource
        return new TimeSlotResource($timeSlot);
    }

    /**
     * Remove the specified availability from storage.
     */
    public function destroy($id)
    {
        // Find the availability by ID
        $timeSlot = TimeSlot::findOrFail($id);

        // Delete the availability
        $timeSlot->delete();

        // Return a success message
        return response()->json(['message' => 'Availability deleted successfully.']);
    }


    public function getDoctorAvailableSlot(Request $request)
    {

        $validatedData = $request->validate([
            'doctor_id' => 'required|integer',
            'service_id' => 'required|integer',
        ]);

        $appointments = TimeSlot::where('doctor_id', $validatedData['doctor_id'])
            ->where('service_id', $validatedData['service_id'])
            ->get();

        return api()->success($appointments);

    }


    public function updateTimeSlotAvailablty(Request $request, $id)
    {

        $validated = $request->validate([
            'is_available' => 'required|boolean',
        ]);
        if (!$validated) {
            return api()->validation('Please enter the requred field');
        }

        $timeSlot = TimeSlot::findOrFail($id);

        $timeSlot->is_available = $validated['is_available'];
        $timeSlot->save();


        return api()->success($timeSlot, 'Time slot updated successfully.');
    }
}
if (!function_exists('api')) {

    function api()
    {
        return new ApiResponse();
    }
}