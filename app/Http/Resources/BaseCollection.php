<?php

namespace App\Http\Resources;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCollection extends ResourceCollection
{
    protected $message;

    public function __construct($resource, $message = "Data retrieved successfully")
    {
        parent::__construct($resource);
        $this->message = $message;
    }

    public function toArray(Request $request): array
    {
        return [
            "status" => true,
            "message" => $this->message,
            "data" => UserResource::collection($this->collection),
        ];
        
    }
    public function paginationInformation($request, $paginated, $default)
    {
        return [];
    }

    public function with($request)
    {
        if (method_exists($this->resource, 'total')) {
            return [
                "pagination" => [
                    "current_page" => $this->currentPage(),
                    "last_page" => $this->lastPage(),
                    "per_page" => $this->perPage(),
                    "total" => $this->total(),
                ]
            ];
        }

        return [];
    }
}