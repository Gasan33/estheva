<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServicesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'images' => $this->images,
            'location' => $this->location,
            'video' => $this->video,
            'duration' => $this->duration,
            'benefits' => $this->benefits,
            'discount_value' => $this->discount_value,
            'discount_type' => $this->discount_type,
            'service_sale_tag' => $this->service_sale_tag,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'doctors' => DoctorResource::collection($this->whenLoaded('doctors')),
            'timeSlots' => TimeSlotResource::collection($this->whenLoaded('timeslots')),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
            'medical_reports' => MedicalReportResource::collection($this->whenLoaded('medicalReports')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'discounted_price' => $this->getDiscountedPrice(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
