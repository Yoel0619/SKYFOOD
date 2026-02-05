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
        'delivery_address_id',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'delivery_fee',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'delivery_person_id',
        'special_instructions',
        'estimated_delivery_time',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'estimated_delivery_time' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Auto-generate order number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryAddress()
    {
        return $this->belongsTo(UserAddress::class, 'delivery_address_id');
    }

    public function deliveryPerson()
    {
        return $this->belongsTo(User::class, 'delivery_person_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    // Update status with history
    public function updateStatus($newStatus, $userId, $remarks = null)
    {
        $oldStatus = $this->order_status;

        $this->update(['order_status' => $newStatus]);

        OrderStatusHistory::create([
            'order_id' => $this->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $userId,
            'remarks' => $remarks,
        ]);

        return $this;
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('order_status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('order_status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('order_status', 'delivered');
    }

    // Helper methods
    public function canBeCancelled()
    {
        return in_array($this->order_status, ['pending', 'confirmed']);
    }

    public function cancel($reason, $userId)
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->update([
            'order_status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        $this->updateStatus('cancelled', $userId, $reason);

        return true;
    }
}