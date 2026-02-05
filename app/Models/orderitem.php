<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'food_id',
        'quantity',
        'unit_price',
        'subtotal',
        'special_instructions',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    // Auto-calculate subtotal
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            if (empty($orderItem->subtotal)) {
                $orderItem->subtotal = $orderItem->quantity * $orderItem->unit_price;
            }
        });
    }
}