<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalReportResource extends JsonResource
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
            'patient' => new UserResource($this->whenLoaded('patient')),
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'treatment' => new TreatmentsResource($this->whenLoaded('treatment')),
            'report_date' => $this->report_date,
            'report_details' => $this->report_details,
            'attachments' => $this->attachments, // Will be cast to array based on your model
        ];
    }
}
