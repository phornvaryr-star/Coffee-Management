<?php

namespace App\Http\Resources;
use App\Models\User;
use App\Http\Resources\UserResource;


class ResourceClass
{
    public static function resolve($model)
    {
        $map = [
            User::class => UserResource::class,
            // Add more models here
        ];
        return $map[get_class($model)] ?? null;
    }
}
