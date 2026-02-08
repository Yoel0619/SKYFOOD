<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationship: User belongs to a role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relationship: User has many orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Check if user is admin
    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }

    // Check if user is customer
    public function isCustomer()
    {
        return $this->role->name === 'customer';
    }
}