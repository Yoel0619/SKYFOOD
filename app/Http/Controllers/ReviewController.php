<?php
namespace App\Http\Controllers;
use App\Models\Review;
use Illuminate\Http\Request;
class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id'=>'required|exists:restaurants,id',
            'rating'=>'required|integer|min:1|max:5'
        ]);

        return Review::create([
            'user_id'=>request()->id(),
            'restaurant_id'=>$request->restaurant_id,
            'rating'=>$request->rating,
            'comment'=>$request->comment
        ]);
    }
}
