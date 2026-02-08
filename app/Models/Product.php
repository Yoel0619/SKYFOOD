<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'stock',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relationship: Product belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship: Product has many order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}