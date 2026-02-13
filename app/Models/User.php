<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash; 

class User extends Authenticatable
{
    use Notifiable; 

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
        $this->attributes['password'] = Hash::make($value);
    }
}
