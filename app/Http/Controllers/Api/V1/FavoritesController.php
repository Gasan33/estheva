<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FavoriteResource;
use App\Models\Favorites;
use Exception;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    /**
     * Display a listing of the user's favorite treatments.
     */
    public function index(Request $request)
    {
        try {
            $favorites = Favorites::with(['treatment'])
                ->where('user_id', $request->user_id)
                ->get();

            return $this->api()->success(FavoriteResource::collection($favorites));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Store a newly created favorite treatment.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'treatment_id' => 'required|exists:treatments,id',
            ]);

            // Prevent duplicate favorites
            $existing = Favorites::where('user_id', $validatedData['user_id'])
                ->where('treatment_id', $validatedData['treatment_id'])
                ->first();

            if ($existing) {
                return $this->api()->success($existing, "Already in favorites.");
            }

            $favorite = Favorites::create($validatedData);

            return $this->api()->created(new FavoriteResource($favorite), "Treatment added to favorites.");
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Display a specific favorite.
     */
    public function show($id)
    {
        try {
            $favorite = Favorites::with(['user', 'treatment'])->findOrFail($id);

            return $this->api()->success(new FavoriteResource($favorite));
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Update a favorite record.
     */
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'sometimes|exists:users,id',
                'treatment_id' => 'sometimes|exists:treatments,id',
            ]);

            $favorite = Favorites::findOrFail($id);
            $favorite->update($validatedData);

            return $this->api()->success(new FavoriteResource($favorite), 'Favorite updated successfully.');
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Remove a treatment from favorites.
     */
    public function destroy(Request $request)
    {
        try {
            $favorite = Favorites::where('user_id', $request->user_id)
                ->where('treatment_id', $request->treatment_id)
                ->firstOrFail();

            $favorite->delete();

            return $this->api()->success([
                'deleted_treatment_id' => $favorite->treatment_id
            ], 'Treatment removed from favorites.');
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }

    /**
     * Check if a treatment is already favorited by the user.
     */
    public function isFavorited(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'treatment_id' => 'required|exists:treatments,id',
            ]);

            $exists = Favorites::where('user_id', $validated['user_id'])
                ->where('treatment_id', $validated['treatment_id'])
                ->exists();

            return response()->json(['favorited' => $exists]);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
    }
}
