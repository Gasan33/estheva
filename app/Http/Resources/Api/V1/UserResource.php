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
            'full_name' => $this->name, // Using the accessor you defined
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'is_admin' => $this->isAdmin(),
            'is_doctor' => $this->isDoctor(),
            'addresses' => AddressResource::collection($this->addresses), // Assuming you have an AddressResource
            'sent_messages' => MessageResource::collection($this->sentMessages), // Assuming you have a MessageResource
            'received_messages' => MessageResource::collection($this->receivedMessages), // Assuming you have a MessageResource
            'medical_reports' => MedicalReportResource::collection($this->medicalReports), // Assuming you have a MedicalReportResource
        ];
    }
}
