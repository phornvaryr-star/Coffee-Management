<?php

namespace App\Http\Resources;
<<<<<<< HEAD
use App\Models\Category;
use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Http\Resources\CustomerResource;
=======
use App\Models\User;
use App\Models\Role;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;

>>>>>>> 281d74e (fix: User & Role Management)

class ResourceClass
{
    public static function resolve($model)
    {
        $map = [
            User::class => UserResource::class,
            // Add more models here
<<<<<<< HEAD
            Product::class => ProductResource::class,
            Category::class => CategoryResource::class,
            Role::class => RoleResource::class,
            Customer::class => CustomerResource::class,
            
=======
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
