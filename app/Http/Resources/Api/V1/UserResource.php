<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            "profile_picture" => $this->profilePictureUrl(),
            "role" => $this->role,
            'is_admin' => $this->isAdmin(),
            'is_doctor' => $this->isDoctor(),
            'age' => $this->getAgeAttribute(),
            'weight' => $this->weight,
            "gender" => $this->gender,
            'nationality' => $this->nationality,
            "date_of_birth" => $this->date_of_birth,
            "phone_verified_at" => $this->phone_verified_at,
            "email_verified_at" => $this->email_verified_at,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            'addresses' => AddressResource::collection($this->addresses), // Assuming you have an AddressResource
            'sent_messages' => MessageResource::collection($this->sentMessages), // Assuming you have a MessageResource
            'received_messages' => MessageResource::collection($this->receivedMessages), // Assuming you have a MessageResource
            'medical_reports' => MedicalReportResource::collection($this->medicalReports), // Assuming you have a MedicalReportResource
        ];
    }
}
