<?php

namespace App\Http\Resources;

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
            'id' => $this->id,
            'category_name' => $this->category_name,
            'status' => $this->status,
            'description' => $this->description,
            'created_at' => optional($this->created_at)->format('Y-m-d h:i:s A'),
            'updated_at' => optional($this->updated_at)->format('Y-m-d h:i:s A'),
        ];
    }
}
