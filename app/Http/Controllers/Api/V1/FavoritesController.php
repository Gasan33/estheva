<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Favorites;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /**
     * Display a listing of the user services.
     */
    public function index(Request $request)
    {
        $favorites = Favorites::with(['service'])
            ->where('user_id', $request->user_id)
            ->get();
        return response()->json($favorites);
    }

    /**
     * Store a newly created user service in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
        ]);

        $favorites = Favorites::create($validatedData);

        return response()->json(['message' => 'User service created successfully!', 'data' => $favorites], 201);
    }

    /**
     * Display the specified user service.
     */
    public function show($id)
    {
        $favorites = Favorites::with(['user', 'service'])->findOrFail($id);
        return response()->json($favorites);
    }

    /**
     * Update the specified user service in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'service_id' => 'sometimes|exists:services,id',
        ]);

        $favorites = Favorites::findOrFail($id);
        $favorites->update($validatedData);

        return response()->json(['message' => 'User service updated successfully!', 'data' => $favorites]);
    }

    /**
     * Remove the specified user service from storage.
     */
    public function destroy(Request $request)
    {
        $favorites = Favorites::with(['service'])
            ->where('user_id', $request->user_id)
            ->where('service_id', $request->service_id)
            ->firstOrFail();
        $favorites->delete();

        return response()->json(['message' => 'User service deleted successfully!']);
    }
}
