<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'patient_id' => $this->user_id,
            'doctor' => $this->doctor_id,
            'treatment' => $this->treatment_id,

            // 'patient' => new UserResource($this->whenLoaded('user')),
            // 'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            // 'treatment' => new TreatmentsResource($this->whenLoaded('treatment')),
            'appointment_date' => $this->appointment_date,
            'appointment_time' => $this->appointment_time,
            'location' => $this->location,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
