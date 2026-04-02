<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        "user_id",
        "phone",
        "address",
        "image",
        "type"
    ];
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->updated_at = null;
        });
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
