<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAppointmentRequest;
use App\Http\Requests\Api\V1\UpdateAppointmentRequest;
use App\Http\Resources\Api\V1\AppointmentResource;
use App\Models\Appointment;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Exception;

class AppointmentsController extends Controller
{

    public function index()
    {
        try {
            $appointments = Appointment::with(['user', 'doctor', 'treatment'])
                // ->select(['id', 'user_id', 'doctor_id', 'treatment_id', 'appointment_date', 'appointment_time', 'status'])
                // ->latest()
                ->get();

            return $this->api()->success(AppointmentResource::collection($appointments));
        } catch (Exception $exception) {
            return response()->json(['message' => 'Failed to fetch appointments.', 'error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Get appointments for a specific user.
     */
    public function userAppointments(Request $request)
    {
        try {
            $appointments = Appointment::with(['user', 'doctor', 'treatment'])
                ->where('user_id', $request->user_id)
                // ->select(['id', 'user_id', 'doctor_id', 'treatment_id', 'appointment_date', 'appointment_time', 'status'])
                // ->latest()
                ->get();

            return $this->api()->success(AppointmentResource::collection($appointments));
        } catch (Exception $exception) {
            return response()->json(['message' => 'Failed to fetch user appointments.', 'error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Store a newly created appointment.
     */
    public function store(StoreAppointmentRequest $request)
    {
        try {
            $validated = $request->validated();

            // Ensure time slot is available before booking
            $timeSlot = TimeSlot::where('id', $validated['time_slot_id'])
                ->where('is_available', true)
                ->firstOrFail();

            $timeSlot->update(['is_available' => false]);

            $appointment = Appointment::create($validated);

            return $this->api()->created(new AppointmentResource($appointment), "Appointment successfully scheduled.");
        } catch (Exception $exception) {
            return response()->json(['message' => 'Failed to schedule appointment.', 'error' => $exception->getMessage()], 400);
        }
    }

    /**
     * Display the specified appointment.
     */
    public function show($id)
    {
        try {
            $appointment = Appointment::with(['user', 'doctor', 'treatment'])
                // ->select(['id', 'user_id', 'doctor_id', 'treatment_id', 'appointment_date', 'appointment_time', 'status'])
                ->findOrFail($id);

            return $this->api()->success(new AppointmentResource($appointment));
        } catch (Exception $exception) {
            return response()->json(['message' => 'Appointment not found.', 'error' => $exception->getMessage()], 404);
        }
    }

    /**
     * Update an existing appointment.
     */
    public function update(UpdateAppointmentRequest $request, $id)
    {
        try {
            $validated = $request->validated();
            $appointment = Appointment::findOrFail($id);

            // Check if time slot needs to be updated
            if (isset($validated['time_slot_id'])) {
                $newTimeSlot = TimeSlot::where('id', $validated['time_slot_id'])
                    ->where('is_available', true)
                    ->firstOrFail();

                // Release old time slot
                TimeSlot::where('id', $appointment->time_slot_id)->update(['is_available' => true]);

                // Assign new time slot
                $newTimeSlot->update(['is_available' => false]);
            }

            $appointment->update($validated);

            return $this->api()->success(new AppointmentResource($appointment), "Appointment updated successfully.");
        } catch (Exception $exception) {
            return response()->json(['message' => 'Failed to update appointment.', 'error' => $exception->getMessage()], 400);
        }
    }

    /**
     * Remove the specified appointment.
     */
    public function destroy($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            // Release the time slot associated with this appointment
            TimeSlot::where('id', $appointment->time_slot_id)->update(['is_available' => true]);

            $appointment->delete();

            return $this->api()->success([], "Appointment deleted successfully.");
        } catch (Exception $exception) {
            return response()->json(['message' => 'Failed to delete appointment.', 'error' => $exception->getMessage()], 400);
        }
    }
}
