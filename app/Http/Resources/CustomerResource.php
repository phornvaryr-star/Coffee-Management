<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'description' => $this->description,
            'created_at' => optional($this->created_at)->format('Y-m-d h:i:s A'),
            'updated_at' => optional($this->updated_at)->format('Y-m-d h:i:s A'),
        ];
    }
}
