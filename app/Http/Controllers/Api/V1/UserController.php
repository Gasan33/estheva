<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['addresses', 'sentMessages', 'receivedMessages', 'medicalReports'])->get();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

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

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::with(['addresses', 'sentMessages', 'receivedMessages', 'medicalReports'])->findOrFail($id);
        return response()->json($user);
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

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    /**
     * Get the full name of the user.
     */
    public function getFullName($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['full_name' => $user->full_name]);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['is_admin' => $user->isAdmin()]);
    }
}
