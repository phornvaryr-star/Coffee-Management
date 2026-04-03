<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            // 'category_id' => $this->category_id,
            'product_name' => $this->product_name,
            'purchase_price' => $this->purchase_price,
            'sale_price' => $this->sale_price,
            'qty' => $this->qty,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => optional($this->created_at)->format('Y-m-d h:i:s A'),
            'updated_at' => optional($this->updated_at)->format('Y-m-d h:i:s A'),
            'category' => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
