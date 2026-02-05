<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FoodController extends Controller
{
  
    // Display foods
    public function index(Request $request)
    {
        $query = Food::with('category');

        if ($request->has('search')) {
            $query->search($request->search);
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $foods = $query->paginate(15);
        $categories = Category::active()->get();

        return view('admin.foods.index', compact('foods', 'categories'));
    }

    // Show create form
    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.foods.create', compact('categories'));
    }

    // Store food
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'preparation_time' => 'nullable|integer|min:0',
            'is_vegetarian' => 'boolean',
            'is_available' => 'boolean',
            'calories' => 'nullable|integer|min:0',
            'ingredients' => 'nullable|string',
            'allergen_info' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'discount_percentage' => $request->discount_percentage ?? 0,
            'preparation_time' => $request->preparation_time ?? 20,
            'is_vegetarian' => $request->is_vegetarian ?? false,
            'is_available' => $request->is_available ?? true,
            'calories' => $request->calories,
            'ingredients' => $request->ingredients,
            'allergen_info' => $request->allergen_info,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('foods', 'public');
            $data['image'] = $path;
        }

        $food = Food::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Food item created successfully',
            'redirect' => route('admin.foods.index')
        ]);
    }

    // Show edit form
    public function edit($id)
    {
        $food = Food::findOrFail($id);
        $categories = Category::active()->get();
        return view('admin.foods.edit', compact('food', 'categories'));
    }

    // Update food
    public function update(Request $request, $id)
    {
        $food = Food::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'preparation_time' => 'nullable|integer|min:0',
            'is_vegetarian' => 'boolean',
            'is_available' => 'boolean',
            'calories' => 'nullable|integer|min:0',
            'ingredients' => 'nullable|string',
            'allergen_info' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'discount_percentage' => $request->discount_percentage ?? 0,
            'preparation_time' => $request->preparation_time ?? 20,
            'is_vegetarian' => $request->is_vegetarian ?? false,
            'is_available' => $request->is_available ?? true,
            'calories' => $request->calories,
            'ingredients' => $request->ingredients,
            'allergen_info' => $request->allergen_info,
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($food->image) {
                Storage::disk('public')->delete($food->image);
            }

            $path = $request->file('image')->store('foods', 'public');
            $data['image'] = $path;
        }

        $food->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Food item updated successfully',
            'redirect' => route('admin.foods.index')
        ]);
    }

    // Delete food
    public function destroy($id)
    {
        $food = Food::findOrFail($id);

        // Delete image
        if ($food->image) {
            Storage::disk('public')->delete($food->image);
        }

        $food->delete();

        return response()->json([
            'success' => true,
            'message' => 'Food item deleted successfully'
        ]);
    }

    // Toggle availability
    public function toggleAvailability($id)
    {
        $food = Food::findOrFail($id);
        $food->is_available = !$food->is_available;
        $food->save();

        return response()->json([
            'success' => true,
            'message' => 'Availability updated',
            'is_available' => $food->is_available
        ]);
    }
}