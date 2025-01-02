<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class AppointmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = Appointment::with(['user', 'doctor', 'service'])->get();
        return api()->success($appointments, );
    }


    public function userAppointments(Request $request)
    {
        $appointments = Appointment::with(['user', 'doctor', 'service'])
            ->where('user_id', $request->user_id)
            ->get();
        return api()->success($appointments, );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:doctors,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::create($validated);
        return api()->created($appointment, "Appointment successfully scheduled.");
        // return response()->json($appointment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $appointment = Appointment::with(['user', 'doctor', 'service'])->findOrFail($id);
        return api()->success($appointment, );
        // return response()->json($appointment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'doctor_id' => 'sometimes|exists:doctors,id',
            'service_id' => 'sometimes|exists:services,id',
            'appointment_date' => 'sometimes|date',
            'appointment_time' => 'sometimes|date_format:H:i',
            'status' => 'sometimes|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update($validated);
        return api()->created($appointment, "Appointment scheduled updated successfully.");
        // return response()->json($appointment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return api()->success($appointment, "Appointment deleted successfully.");


        // return response()->json(['message' => 'Appointment deleted successfully.']);
    }
}


if (!function_exists('api')) {

    function api()
    {
        return new ApiResponse();
    }
}