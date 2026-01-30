<?php
namespace App\Http\Controllers;
use App\Models\Delivery;
use Illuminate\Http\Request;
class DeliveryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id'=>'required|exists:orders,id',
            'address'=>'required'
        ]);

        return Delivery::create($request->all());
    }
}
