<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Advertisements;
use Illuminate\Http\Request;

class AdvertisementsController extends Controller
{
    /**
     * Display a listing of the advertisements.
     */
    public function index()
    {
        $advertisements = Advertisements::all();
        return response()->json($advertisements);
    }

    /**
     * Store a newly created advertisement in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'ad_title' => 'required|string|max:255',
            'ad_description' => 'required|string',
            'ad_picture' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $advertisement = Advertisements::create($validatedData);
        return response()->json($advertisement, 201);
    }

    /**
     * Display the specified advertisement.
     */
    public function show($id)
    {
        $advertisement = Advertisements::findOrFail($id);
        return response()->json($advertisement);
    }

    /**
     * Update the specified advertisement in storage.
     */
    public function update(Request $request, $id)
    {
        $advertisement = Advertisements::findOrFail($id);

        $validatedData = $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'ad_title' => 'required|string|max:255',
            'ad_description' => 'required|string',
            'ad_picture' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $advertisement->update($validatedData);
        return response()->json($advertisement);
    }

    /**
     * Remove the specified advertisement from storage.
     */
    public function destroy($id)
    {
        $advertisement = Advertisements::findOrFail($id);
        $advertisement->delete();

        return response()->json(['message' => 'Advertisement deleted successfully.']);
    }
}
