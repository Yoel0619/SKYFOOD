<?php
namespace App\Http\Controllers;
use App\Models\Restaurant;
use Illuminate\Http\Request;
class RestaurantController extends Controller
{
    public function index()
    {
        return Restaurant::all();
    }

    public function view()
{
    $restaurants = Restaurant::all();
    return view('restaurant.index', compact('restaurants'));
}
    public function detailedView($id)
    {
        $restaurant = Restaurant::with('menus.items.reviews')->findOrFail($id);
        return view('restaurant.detail', compact('restaurant'));
    }
    public function show($id)
    {
        return Restaurant::findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'location'=>'required',
            'phone'=>'required'
        ]);

        return Restaurant::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->update($request->all());
        return $restaurant;
    }

    public function destroy($id)
    {
        Restaurant::destroy($id);
        return response()->json(['message'=>'Deleted']);
    }
}
