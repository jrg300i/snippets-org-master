<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'thiscodeworks_api_key',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'thiscodeworks_api_key',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function snippets()
    {
        return $this->hasMany(Snippet::class);
    }

    public function recentSnippets()
    {
        return $this->hasMany(Snippet::class)->latest()->limit(5);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}