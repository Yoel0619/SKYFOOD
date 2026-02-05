<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()->ordered()->take(6)->get();
        $featuredFoods = Food::available()
            ->with('category')
            ->withCount('reviews')
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact('categories', 'featuredFoods'));
    }
}