<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash; 
use Laravel\Sanctum\HasApiTokens; // Added for API Authentication

class User extends Authenticatable
{
    use Notifiable, HasApiTokens; // Added HasApiTokens

    protected $connection = 'mongodb';
    protected $collection = 'user';    

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'role', 
        'is_enabled',
        'cart'
    ];

    /**
     * Set default attributes.
     * Equivalent to setting default values in a Java constructor.
     */
    protected $attributes = [
        'role' => 'USER',
        'is_enabled' => false,
        'cart' => []
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // --- Mutators (Setters) ---

    protected function setNameAttribute($value): void 
    {
        $this->attributes['name'] = !is_null($value) ? strtolower(trim($value)) : null;
    }

    protected function setEmailAttribute($value): void
    {
        $this->attributes['email'] = !is_null($value) ? strtolower(trim($value)) : null;
    }

    protected function setPasswordAttribute($value): void
    {
        // Only hash if the value isn't already a hash (to prevent double hashing)
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }
}
