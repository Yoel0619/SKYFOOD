<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Restaurant extends Model
{
    protected $fillable = ['name', 'location', 'phone'];

    public function menus() {
        return $this->hasMany(Menu::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }
}
