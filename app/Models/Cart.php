<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    protected $fillable = [
        'user_id',
        'food_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    // Accessors
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->food->final_price;
    }

    // Helper methods
    public function increaseQuantity($amount = 1)
    {
        $this->increment('quantity', $amount);
    }

    public function decreaseQuantity($amount = 1)
    {
        if ($this->quantity > $amount) {
            $this->decrement('quantity', $amount);
        } else {
            $this->delete();
        }
    }
}