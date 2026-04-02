<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';
    public $timestamps = false;
    protected $fillable = ['user_id', 'role_id'];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->updated_at = null;
        });
    }

}
