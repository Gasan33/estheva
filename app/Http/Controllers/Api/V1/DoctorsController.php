<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DoctorResource;
use App\Models\Availability;
use App\Models\Doctor;
use App\Models\User;
use App\Services\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorsController extends Controller
{
    public function index()
    {
        try {
            $doctors = Doctor::with(['user', 'availabilities', 'addresses', 'medicalReports', 'appointments', 'reviews'])->get();
            return $this->api()->success(DoctorResource::collection($doctors));
        } catch (Exception $exception) {
            return $this->api()->error($exception->getMessage());
        }
    }

    public function store(Request $request)
    {
        // Validation (use custom validation rules or form request classes)
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|unique:users,phone_number',
            'password' => 'required|string|min:8',
            'gender' => 'required|string',
            'date_of_birth' => 'required|date',
            'profile_picture' => 'nullable|image',
            'device_token' => 'nullable|string',
            'specialty' => 'required|string',
            'certificate' => 'nullable|string',
            'university' => 'nullable|string',
            'patients' => 'nullable|integer',
            'exp' => 'nullable|integer',
            'about' => 'nullable|string',
            'home_based' => 'nullable|boolean',
            'availability' => 'required|array',
            'availability.*.day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'availability.*.start_time' => 'required|date_format:H:i',
            'availability.*.end_time' => 'required|date_format:H:i',
        ]);

        try {
            // Create User
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'role' => 'doctor',
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'profile_picture' => $request->profile_picture,
                'device_token' => $request->device_token,
            ]);

            // Create Doctor
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'specialty' => $request->specialty,
                'certificate' => $request->certificate,
                'university' => $request->university,
                'patients' => $request->patients,
                'exp' => $request->exp,
                'about' => $request->about,
                'home_based' => $request->home_based,
            ]);

            // Handle Availability
            foreach ($request->availability as $entry) {
                Availability::create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $entry['day'],
                    'start_time' => $entry['start_time'],
                    'end_time' => $entry['end_time'],
                ]);
            }

            return $this->api()->success(new DoctorResource($doctor)); // Return the created doctor as a resource
        } catch (Exception $exception) {
            return $this->api()->error($exception->getMessage()); // Catch and return any exceptions
        }
    }

    public function show($id)
    {
        try {
            $doctor = Doctor::with(['user', 'availabilities', 'addresses', 'medicalReports', 'appointments', 'reviews'])->findOrFail($id);
            return $this->api()->success(new DoctorResource($doctor)); // Return doctor as a resource
        } catch (Exception $exception) {
            return $this->api()->error('Doctor not found'); // Handle error if doctor is not found
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email',
            'phone_number' => 'sometimes|required|string',
            'password' => 'sometimes|required|string|min:8',
            'specialty' => 'sometimes|required|string',
            'certificate' => 'nullable|string',
            'university' => 'nullable|string',
            'patients' => 'nullable|integer',
            'exp' => 'nullable|integer',
            'about' => 'nullable|string',
            'home_based' => 'nullable|boolean',
            'availability' => 'sometimes|array',
            'availability.*.day' => 'sometimes|required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'availability.*.start_time' => 'sometimes|required|date_format:H:i',
            'availability.*.end_time' => 'sometimes|required|date_format:H:i',
        ]);

        try {
            $doctor = Doctor::findOrFail($id);
            $user = User::find($doctor->user_id);

            // Update user
            $user->update($request->only([
                'first_name',
                'last_name',
                'email',
                'phone_number',
                'password',
                'gender',
                'date_of_birth',
                'profile_picture',
                'device_token'
            ]));

            // Update doctor
            $doctor->update($request->only([
                'specialty',
                'certificate',
                'university',
                'patients',
                'exp',
                'about',
                'home_based'
            ]));

            // Update availability
            if ($request->has('availability')) {
                $doctor->availability()->delete(); // Delete existing availability
                foreach ($request->availability as $entry) {
                    Availability::create([
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $entry['day'],
                        'start_time' => $entry['start_time'],
                        'end_time' => $entry['end_time'],
                    ]);
                }
            }

            return $this->api()->success(new DoctorResource($doctor), 'Doctor updated successfully.');
        } catch (Exception $exception) {
            return $this->api()->error($exception->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $doctor = Doctor::findOrFail($id);
            $doctor->delete();
            return $this->api()->success(null, 'Doctor deleted successfully.');
        } catch (Exception $exception) {
            return $this->api()->error('Doctor not found or deletion failed.');
        }
    }
}
