<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function user()
    {
        return $this->api()->success(Auth::user());
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['addresses', 'sentMessages', 'receivedMessages', 'medicalReports'])->get();
        return $this->api()->success(UserResource::collection($users));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone_number' => 'required|string|unique:users,phone_number',
                'password' => 'required|string|min:6|confirmed',
                'role' => 'required|in:patient,doctor,admin',
                'gender' => 'required|in:male,female',
                'date_of_birth' => 'nullable|date',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'device_token' => 'nullable|string',
            ]);

            if ($request->hasFile('profile_picture')) {
                $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                $validated['profile_picture'] = $profilePicturePath;
            }

            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            return $this->api()->success(UserResource::collection($user), 201);
        } catch (Exception $exception) {
            return response(
                ["message" => $exception->getMessage()],
                400
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with(['addresses', 'sentMessages', 'receivedMessages', 'medicalReports'])->findOrFail($id);
        return $this->api()->success(UserResource::collection($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone_number' => 'sometimes|string|max:20',
            'password' => 'sometimes|string|min:8',
            'role' => 'nullable|in:patient,doctor,admin',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'device_token' => 'nullable|string',
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete the old profile picture if it exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Store the new profile picture
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $profilePicturePath;
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }


        $user->update($validated);

        return $this->api()->success(UserResource::collection($user));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Delete the profile picture if it exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->delete();

        return $this->api()->success([], 'User deleted successfully.');
    }


    public function uploadProfilePic(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        // Optional: validate file type and size
        $request->validate([
            'file' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Store in public disk (storage/app/public/uploads)
        $path = $file->store('uploads', 'public');

        return response()->json([
            'message' => 'File uploaded successfully',
            'path' => '/storage/' . $path,
        ]);
    }
}
