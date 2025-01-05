<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ServicesResource;
use App\Models\Availability;
use App\Models\Doctor;
use App\Models\Services;
use App\Services\ApiResponse;
use App\Services\TimeSlotService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Services::with(['category', 'doctors', 'timeSlots', 'reviews'])->get();

        // return api()->success(ServicesResource::collection($services));
        return api()->success($services);
        // return response()->json($items);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return a view or response for creating a new resource
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'images' => 'nullable|array',
            'doctor_id' => 'nullable|array', // Allow multiple doctor IDs
            'home_based' => 'sometimes|boolean',
            'video' => 'nullable|string',
            'duration' => 'nullable|numeric',
            'benefits' => 'nullable|array',
            'discount_value' => 'nullable|numeric',
            'discount_type' => 'nullable|in:percentage,fixed',
            'service_sale_tag' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $item = Services::create($validatedData);



        if (!empty($request->doctors)) {
            $item->doctors()->sync($request->doctors);
            $item->doctors()->attach($request->doctors);
        }
        if ($item->duration != null) {
            foreach ($request->doctors as $doctorId) {
                $doctoravailabilities = Availability::where('doctor_id', $doctorId)->get();
                if (!$doctoravailabilities) {
                    $this->error('doctor not found.');
                    return;
                }
                foreach ($doctoravailabilities as $availability) {
                    $date = Carbon::now()->toDateString();
                    $startTime = $availability->start_time;
                    $endTime = $availability->end_time;
                    TimeSlotService::generateSlots($doctorId, $item->id, $date, $startTime, $endTime, $item->duration);
                }
            }
        }

        $service = Services::with(['category', 'doctors', 'reviews'])->findOrFail($item->id);
        if ($service) {
            return api()->created($service, "Service Created Successfully.");
        } else {
            return api()->notFound('Service Not Found');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = Services::with(['category', 'doctors', 'reviews'])->findOrFail($id);
        if ($item) {
            return response()->json($item);
        } else {
            return api()->notFound('Service Not Found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Return a view or response for editing the resource
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = Services::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'images' => 'nullable|array',
            'doctor_id' => 'nullable|array', // Allow multiple doctor IDs
            'home_based' => 'sometimes|boolean',
            'video' => 'nullable|string',
            'duration' => 'nullable|numeric',
            'benefits' => 'nullable|array',
            'discount_value' => 'nullable|numeric',
            'discount_type' => 'nullable|in:percentage,fixed',
            'service_sale_tag' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $item->update($validatedData);

        if (!empty($request->doctors)) {
            $item->doctors()->sync($request->doctors);
            $item->doctors()->attach($request->doctors);
        }

        $service = Services::with(['category', 'doctors', 'reviews'])->find($item->id);
        if ($service) {
            return api()->success($service, "Service Updated Successfully.");
        } else {
            return api()->notFound('Service Not Found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $service = Services::with(['category', 'doctors', 'reviews'])->find($id);

        if ($service) {
            $service->delete();
            return api()->success($service, 'Service deleted successfully');
        } else {
            return api()->notFound('Service Not Found');
        }
    }

    /**
     * Get the discounted price of a specific item.
     */
    public function getDiscountedPrice($id)
    {
        $item = Services::findOrFail($id);
        return response()->json(['discounted_price' => $item->getDiscountedPrice()]);
    }
}


if (!function_exists('api')) {

    function api()
    {
        return new ApiResponse();
    }
}