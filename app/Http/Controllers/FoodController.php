<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FoodController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensure user is logged in
    }

    // List all foods
    public function index()
    {
        $foods = Food::latest()->paginate(10);
        return view('admin.foods.index', compact('foods'));
    }

    // Show create form
    public function create()
    {
        return view('admin.foods.create');
    }

    // Store new food
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'description'=>'nullable|string',
            'price'=>'required|numeric',
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/foods'), $filename);
            $data['image'] = $filename;
        }

        Food::create($data);

        return redirect()->route('foods.index')->with('success', 'Food added successfully!');
    }

    // Show edit form
    public function edit(Food $food)
    {
        return view('admin.foods.edit', compact('food'));
    }

    // Update food
    public function update(Request $request, Food $food)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'description'=>'nullable|string',
            'price'=>'required|numeric',
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('images/foods'), $filename);
            $data['image'] = $filename;
        }

        $food->update($data);

        return redirect()->route('foods.index')->with('success', 'Food updated successfully!');
    }

    // Delete food
    public function destroy(Food $food)
    {
        $food->delete();
        return redirect()->route('foods.index')->with('success', 'Food deleted successfully!');
    }
}
