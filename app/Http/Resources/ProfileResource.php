<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'phone' => $this->phone,
            'address' => $this->address,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'created_at' => optional($this->created_at)->format('Y-m-d h:i:s A'),
            'updated_at' => optional($this->updated_at)->format('Y-m-d h:i:s A'),
        ];
    }
}
