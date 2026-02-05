<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Food extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_percentage',
        'image',
        'preparation_time',
        'is_vegetarian',
        'is_available',
        'calories',
        'ingredients',
        'allergen_info',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_vegetarian' => 'boolean',
        'is_available' => 'boolean',
    ];

    protected $appends = ['final_price', 'discount_amount'];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($food) {
            if (empty($food->slug)) {
                $food->slug = Str::slug($food->name);
            }
        });

        static::updating(function ($food) {
            if ($food->isDirty('name') && empty($food->slug)) {
                $food->slug = Str::slug($food->name);
            }
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessors
    public function getFinalPriceAttribute()
    {
        if ($this->discount_percentage > 0) {
            return $this->price - ($this->price * ($this->discount_percentage / 100));
        }
        return $this->price;
    }

    public function getDiscountAmountAttribute()
    {
        if ($this->discount_percentage > 0) {
            return $this->price * ($this->discount_percentage / 100);
        }
        return 0;
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeVegetarian($query)
    {
        return $query->where('is_vegetarian', true);
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }
}