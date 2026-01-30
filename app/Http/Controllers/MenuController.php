<?php
namespace App\Http\Controllers;
use App\Models\Menu;
use Illuminate\Http\Request;
class MenuController extends Controller
{
    public function index()
    {
        return Menu::with('items')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id'=>'required|exists:restaurants,id',
            'name'=>'required'
        ]);

        return Menu::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update($request->all());
        return $menu;
    }

    public function destroy($id)
    {
        Menu::destroy($id);
        return response()->json(['message'=>'Deleted']);
    }
}
