<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    // Display menu page
    public function index(Request $request)
    {
        $categories = Category::active()->ordered()->get();
        
        $query = Food::available()->with(['category', 'reviews']);

        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        // Filter by vegetarian
        if ($request->has('vegetarian') && $request->vegetarian == '1') {
            $query->vegetarian();
        }

        // Price range
        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price != '') {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $foods = $query->paginate(12)->withQueryString();

        return view('menu.index', compact('foods', 'categories'));
    }

    // Show single food item
    public function show($slug)
    {
        $food = Food::where('slug', $slug)
            ->with(['category', 'reviews.user'])
            ->firstOrFail();

        $relatedFoods = Food::available()
            ->where('category_id', $food->category_id)
            ->where('id', '!=', $food->id)
            ->take(4)
            ->get();

        return view('menu.show', compact('food', 'relatedFoods'));
    }

    // API: Get foods (for AJAX)
    public function getFoods(Request $request)
    {
        $query = Food::available()->with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        $foods = $query->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $foods
        ]);
    }
}