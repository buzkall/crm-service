<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = ['name', 'email', 'password']; // is_admin is not allowed to set with mass assignment
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['is_admin' => 'boolean'];

}
