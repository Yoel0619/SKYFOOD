<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'delivery_address',
        'phone',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    // Generate unique order number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = 'ORD-' . strtoupper(uniqid());
        });
    }

    // Relationship: Order belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Order has many order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relationship: Order has one payment
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Relationship: Order has one delivery
    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }
}