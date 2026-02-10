<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'delivery_person_id',
        'delivery_address',
        'phone',
        'status',
        'delivery_notes',
        'assigned_at',
        'picked_up_at',
        'delivered_at',
        'delivery_fee',
        'tracking_number',
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'assigned_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Generate unique tracking number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($delivery) {
            $delivery->tracking_number = 'TRK-' . strtoupper(uniqid());
        });
    }

    // Relationship: Delivery belongs to an order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship: Delivery belongs to a delivery person (user)
    public function deliveryPerson()
    {
        return $this->belongsTo(User::class, 'delivery_person_id');
    }
}