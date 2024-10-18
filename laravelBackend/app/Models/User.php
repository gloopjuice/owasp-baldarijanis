<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Services\ChangeTrackingService;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'bio',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            app(ChangeTrackingService::class)->logChange("User {$user->id} created: {$user->name} ({$user->email})");
        });

        static::updated(function ($user) {
            app(ChangeTrackingService::class)->logChange("User {$user->id} updated: {$user->name} ({$user->email})");
        });

        static::deleted(function ($user) {
            app(ChangeTrackingService::class)->logChange("User {$user->id} deleted: {$user->name} ({$user->email})");
        });
    }
}