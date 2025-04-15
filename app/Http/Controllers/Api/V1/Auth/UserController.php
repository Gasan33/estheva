<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Exception;

class UserController extends Controller
{
    public function user()
    {
        return $this->api()->success(new UserResource(Auth::user()));
    }

    public function index()
    {
        $users = User::with(['addresses', 'sentMessages', 'receivedMessages', 'medicalReports'])->get();
        return $this->api()->success(UserResource::collection($users));
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            return $this->api()->success(new UserResource($user), 201);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        $user = User::with(['addresses', 'sentMessages', 'receivedMessages', 'medicalReports'])->findOrFail($id);
        return $this->api()->success(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $validated = $request->validated();

            $user->update($validated);

            return $this->api()->success(new UserResource($user));
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $user->delete();

            return $this->api()->success([], 'User deleted successfully.');
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    public function uploadProfilePic(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->store('profile_pictures', 'public');

        return response()->json([
            'message' => 'File uploaded successfully',
            'path' => '/storage/' . $path,
        ]);
    }
}
