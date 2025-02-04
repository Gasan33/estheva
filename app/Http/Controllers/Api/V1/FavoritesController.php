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
        $favorites = Favorites::with(['treatment'])
            ->where('user_id', $request->user_id)
            ->get();
        return response()->json($favorites);
    }

    /**
     * Store a newly created user treatment in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'treatment_id' => 'required|exists:treatments,id',
        ]);

        $favorites = Favorites::create($validatedData);

        return response()->json(['message' => 'User treatment created successfully!', 'data' => $favorites], 201);
    }

    /**
     * Display the specified user treatment.
     */
    public function show($id)
    {
        $favorites = Favorites::with(['user', 'treatment'])->findOrFail($id);
        return response()->json($favorites);
    }

    /**
     * Update the specified user treatment in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'treatment_id' => 'sometimes|exists:treatments,id',
        ]);

        $favorites = Favorites::findOrFail($id);
        $favorites->update($validatedData);

        return response()->json(['message' => 'User treatment updated successfully!', 'data' => $favorites]);
    }

    /**
     * Remove the specified user treatment from storage.
     */
    public function destroy(Request $request)
    {
        $favorites = Favorites::with(['treatment'])
            ->where('user_id', $request->user_id)
            ->where('treatment_id', $request->treatment_id)
            ->firstOrFail();
        $favorites->delete();

        return response()->json(['message' => 'User treatment deleted successfully!']);
    }
}
