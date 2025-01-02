<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'message_text' => $this->message_text,
            'is_read' => $this->is_read,
            'sender' => new UserResource($this->whenLoaded('sender')), // Assuming you have a UserResource
            'receiver' => new UserResource($this->whenLoaded('receiver')), // Assuming you have a UserResource
        ];
    }
}
