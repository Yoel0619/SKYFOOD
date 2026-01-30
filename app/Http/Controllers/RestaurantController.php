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
