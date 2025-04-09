<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
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
            'doctor_id' => $this->doctor_id,
            'treatment_id' => $this->treatment_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'treatment' => TreatmentsResource::collection($this->whenLoaded('treatments')),
        ];
    }
}
