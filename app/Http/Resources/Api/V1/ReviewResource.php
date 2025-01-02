<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'patient' => new UserResource($this->whenLoaded('patient')), // Assuming you have a UserResource for patient
            'doctor' => new DoctorResource($this->whenLoaded('doctor')), // Assuming you have a DoctorResource
            'service' => new ServicesResource($this->whenLoaded('service')), // Assuming you have a ServiceResource
            'rating' => $this->rating,
            'review_text' => $this->review_text,
        ];
    }
}
