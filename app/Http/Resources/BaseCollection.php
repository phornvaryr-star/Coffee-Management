<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseCollection extends ResourceCollection
{
    protected $message;
    protected $resourceClass;
    public function getMessage()
    {
        return $this->message;
    }
    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }

    public function __construct($resource, $resourceClass = null, $message = "Data retrieved successfully")
    {
        parent::__construct($resource);
        $this->message = $message;

        $this->resourceClass = $resourceClass 
            ?? ResourceClass::resolve($resource->first());
    }

    public function toArray(Request $request): array
    {
        return [
            "status" => true,
            "message" => $this->message,
            "data" => $this->resourceClass::collection($this->collection),
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
                    'pageSize'         => $this->perPage(),
                    'pageNumber'       => $this->currentPage(),
                    'totalPages'       => $this->lastPage(),
                    'totalElements'    => $this->total(),
                    'numberOfElements' => $this->count(),
                    'first'            => $this->onFirstPage(),
                    'last'             => !$this->hasMorePages(),
                    'empty'            => $this->isEmpty(),
                ]
            ];
        }

        return [];
    }
}