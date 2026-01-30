<?php
namespace App\Http\Controllers;
use App\Models\Payment;
use Illuminate\Http\Request;
class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id'=>'required|exists:orders,id',
            'method'=>'required'
        ]);

        return Payment::create($request->all());
    }
}
