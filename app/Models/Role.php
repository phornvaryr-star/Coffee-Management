<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
class Role extends Model
{
    protected $fillable = ['name'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->updated_at = null;
        });
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }

}
