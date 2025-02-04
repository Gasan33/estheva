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
            'user_id' => $this->user_id,
            'specialty' => $this->specialty,
            'certificate' => $this->certificate,
            'university' => $this->university,
            'patients' => $this->patients,
            'exp' => $this->exp,
            'about' => $this->about,
            'home_based' => $this->home_based,
            'availabilities' => $this->availabilities,
            'user' => $this->user,
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),

            // 'availabilities' => AvailabilityResource::collection($this->whenLoaded('availabilities')),
            'treatments' => TreatmentsResource::collection($this->whenLoaded('treatments')), // Return a collection of Treatment resources
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')), // Return reviews
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')), // Return appointments
            'medical_reports' => MedicalReportResource::collection($this->whenLoaded('medicalReports')),

        ];
    }
}
