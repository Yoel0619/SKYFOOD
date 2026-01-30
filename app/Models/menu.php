<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Menu extends Model
{
    protected $fillable = ['restaurant_id', 'name', 'price', 'description'];

    public function restaurant() {
        return $this->belongsTo(Restaurant::class);
    }
}
