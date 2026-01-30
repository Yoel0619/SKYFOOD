<?php
namespace App\Http\Controllers;
use App\Models\MenuItem;
use Illuminate\Http\Request;
class MenuItemController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'menu_id'=>'required|exists:menus,id',
            'name'=>'required',
            'price'=>'required|numeric'
        ]);

        return MenuItem::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $item = MenuItem::findOrFail($id);
        $item->update($request->all());
        return $item;
    }

    public function destroy($id)
    {
        MenuItem::destroy($id);
        return response()->json(['message'=>'Deleted']);
    }
}
