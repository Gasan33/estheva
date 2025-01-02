<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'type' => 'category',
            'category_id' => $this->id,
            'category_name' => $this->name,
            'category_slug' => $this->slug,
            'category_description' => $this->description,
            'parent_id' => $this->parent_id,
            'relations' => [
                'images' => new ImageResource($this->image)
            ]
        ];
    }
}
