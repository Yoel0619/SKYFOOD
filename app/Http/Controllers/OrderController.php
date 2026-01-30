<?php
namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
class OrderController extends Controller
{
    public function index()
    {
        return Order::with('items')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'total'=>'required|numeric'
        ]);

        return Order::create([
            'user_id'=>$request()->id(),
            'total'=>$request->total
        ]);
    }

    public function update(Request $request,$id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->all());
        return $order;
    }
}
