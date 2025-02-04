<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreatmentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'discounted_price' => $this->getDiscountedPrice(),
            'images' => $this->images,
            'home_based' => $this->home_based,
            'video' => $this->video,
            'duration' => $this->duration,
            'benefits' => $this->benefits,
            'instructions' => $this->instructions,
            'discount_value' => $this->discount_value,
            'discount_type' => $this->discount_type,
            'treatment_sale_tag' => $this->treatment_sale_tag,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'doctors' => $this->doctors ? DoctorResource::collection($this->whenLoaded('doctors')) : [],
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'time_slots' => TimeSlotResource::collection($this->whenLoaded('timeSlots')),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
            'medical_reports' => MedicalReportResource::collection($this->whenLoaded('medicalReports')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
