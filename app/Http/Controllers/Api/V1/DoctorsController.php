<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\services;
use App\Models\User;
use App\Services\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctor::with(['user', 'addresses', 'availability', 'appointments', 'medicalReports', 'reviews'])->get();
        return response()->json($doctors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'user_id' => 'required|exists:users,id',
        //     'specialty' => 'required|string|max:255',
        //     'certificate' => 'nullable|string',
        //     'university' => 'nullable|string',
        //     'patients' => 'nullable|integer',
        //     'exp' => 'nullable|integer',
        //     'about' => 'nullable|string',
        //     'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        //     'start_time' => 'required|date_format:H:i',
        //     'end_time' => 'required|date_format:H:i',
        // ]);
        // 'email' => $request->email,

        // if (Doctor::where('user_id', $request->user_id)->exists()) {
        //     return api()->validation([
        //         'message' => 'Doctor already exists please try again.',

        //     ], );
        // }


        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,

            'password' => Hash::make(
                $request->password
            ),
            'role' => 'doctor',
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'profile_picture' => $request->profile_picture,
            'device_token' => $request->device_token,
        ]);



        $doctor = Doctor::create([
            'user_id' => $user->id,
            'specialty' => $request->specialty,
            'certificate' => $request->certificate,
            'university' => $request->university,
            'patients' => $request->patients,
            'exp' => $request->exp,
            'about' => $request->about,
            'home_based' => $request->home_based,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return api()->success($doctor);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $doctor = Doctor::with(['user', 'addresses', 'availability', 'appointments', 'medicalReports', 'reviews'])->findOrFail($id);
        return response()->json($doctor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $validated = $request->validate([
        //     'user_id' => 'sometimes|exists:users,id',
        //     'specialty' => 'sometimes|string|max:255',
        //     'certificate' => 'nullable|string',
        //     'university' => 'nullable|string',
        //     'patients' => 'nullable|integer',
        //     'exp' => 'nullable|integer',
        //     'about' => 'nullable|string',
        //     'day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
        //     'start_time' => 'required|date_format:H:i',
        //     'end_time' => 'required|date_format:H:i',
        // ]);

        $doctor = Doctor::find($id);
        if (!$doctor) {
            return api()->notFound();
        }

        $user = User::find($doctor->user_id);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make(
                $request->password
            ),
            'role' => $request->role,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'profile_picture' => $request->profile_picture,
            'device_token' => $request->device_token,
        ]);

        $doctor->update([
            'user_id' => $user->id,
            'specialty' => $request->specialty,
            'certificate' => $request->certificate,
            'university' => $request->university,
            'patients' => $request->patients,
            'exp' => $request->exp,
            'about' => $request->about,
            'home_based' => $request->home_based,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return api()->success($doctor, "Doctor updated successfully.");

        // return response()->json($doctor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $doctor = Doctor::find($id);
        if (!$doctor) {
            return api()->notFound();
        }
        $doctor->delete();


        return response()->json(['message' => 'Doctor deleted successfully.', 'doctor' => $doctor, 'code' => 200]);
    }


}


if (!function_exists('api')) {

    function api()
    {
        return new ApiResponse();
    }
}

