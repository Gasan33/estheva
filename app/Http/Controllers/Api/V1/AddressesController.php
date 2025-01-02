<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AddressResource;
use App\Models\Addresses;
use Illuminate\Http\Request;

class AddressesController extends Controller
{
    /**
     * Display a listing of the addresses.
     */
    public function index()
    {
        // Get all addresses with the related 'user' and 'addressable' models
        $addresses = Addresses::with(['user', 'addressable'])->get();

        // Return the addresses as a resource collection
        return AddressResource::collection($addresses);
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'addressable_id' => 'required|integer',
            'addressable_type' => 'required|string',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'is_primary' => 'required|boolean',
        ]);

        // Create the new address
        $address = Addresses::create($validated);

        // Return the newly created address as a resource
        return new AddressResource($address);
    }

    /**
     * Display the specified address.
     */
    public function show($id)
    {
        // Find the address by ID, including the related 'user' and 'addressable' models
        $address = Addresses::with(['user', 'addressable'])->findOrFail($id);

        // Return the address as a resource
        return new AddressResource($address);
    }

    /**
     * Update the specified address in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'addressable_id' => 'sometimes|integer',
            'addressable_type' => 'sometimes|string',
            'address_line_1' => 'sometimes|string|max:255',
            'address_line_2' => 'sometimes|nullable|string|max:255',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'postal_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:100',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
            'is_primary' => 'sometimes|boolean',
        ]);

        // Find the address by ID
        $address = Addresses::findOrFail($id);

        // Update the address with the validated data
        $address->update($validated);

        // Return the updated address as a resource
        return new AddressResource($address);
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy($id)
    {
        // Find the address by ID
        $address = Addresses::findOrFail($id);

        // Delete the address
        $address->delete();

        // Return a success message
        return response()->json(['message' => 'Address deleted successfully.']);
    }
}
