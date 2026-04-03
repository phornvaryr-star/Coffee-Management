<?php

namespace App\Http\Resources;
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
class ResourceClass
{
    public static function resolve($model)
    {
        $map = [
            User::class => UserResource::class,
            Product::class => ProductResource::class,
            Category::class => CategoryResource::class,
            Role::class => RoleResource::class,
            Customer::class => CustomerResource::class,
        ];
        return $map[get_class($model)] ?? null;
    }
}
