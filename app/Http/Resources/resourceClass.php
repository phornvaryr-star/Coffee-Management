<?php

namespace App\Http\Resources;
use App\Models\User;
use App\Models\Role;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
class ResourceClass
{
    public static function resolve($model)
    {
        $map = [
            User::class => UserResource::class,
            // Add more models here
            Role::class => RoleResource::class,  
>>>>>>> 281d74e (fix: User & Role Management)
        ];

        return $map[get_class($model)] ?? null;
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 281d74e (fix: User & Role Management)
