<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'specialty' => $this->specialty,
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'availability' => AvailabilityResource::collection($this->whenLoaded('availability')),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
            'medical_reports' => MedicalReportResource::collection($this->whenLoaded('medicalReports')),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
