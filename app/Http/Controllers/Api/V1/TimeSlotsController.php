<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TimeSlotResource;
use App\Models\Availability;
use App\Models\TimeSlot;
use App\Models\Treatment;
use App\Services\TimeSlotService;
use Exception;
use Illuminate\Http\Request;

class TimeSlotsController extends Controller
{
    /**
     * Display a listing of the availabilities.
     */
    public function index()
    {
        // Retrieve all availabilities, including the related 'doctor' and 'treatment' models
        $timeSlot = TimeSlot::with(['doctor', 'treatment'])->get();

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
            'treatment_id' => 'required|exists:treatments,id',
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
        // Find the availability by ID, including the related 'doctor' and 'treatment'
        $timeSlot = TimeSlot::with(['doctor', 'treatment'])->findOrFail($id);

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
            'treatment_id' => 'sometimes|exists:treatments,id',
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
        try {
            $treatment = Treatment::with(['doctors', 'timeSlots'])->findOrFail($request->treatment_id);

            if ($treatment->timeSlots->isEmpty()) {
                $availabilities = Availability::where('doctor_id', $request->doctor_id)->get();
                // dd($availabilities);
                foreach ($availabilities as $availability) {
                    TimeSlotService::generateSlots(
                        $request->doctor_id,
                        $treatment->id,
                        $request->date,
                        $availability->start_time,
                        $availability->end_time,
                        $treatment->duration
                    );
                }
            }

            $timeSlots = TimeSlot::with(['doctor', 'treatment'])
                ->where('treatment_id', $treatment->id)
                ->get();

            return $this->api()->success(TimeSlotResource::collection($timeSlots));

        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }

    }


    public function updateTimeSlotAvailablty(Request $request, $id)
    {

        $validated = $request->validate([
            'is_available' => 'required|boolean',
        ]);
        if (!$validated) {
            return $this->api()->validation('Please enter the requred field');
        }

        $timeSlot = TimeSlot::findOrFail($id);

        $timeSlot->is_available = $validated['is_available'];
        $timeSlot->save();


        return $this->api()->success($timeSlot, 'Time slot updated successfully.');
    }
}