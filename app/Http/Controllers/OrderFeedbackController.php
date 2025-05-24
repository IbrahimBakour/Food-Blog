<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderFeedback; // Create this model if you don't have it yet
use Illuminate\Support\Facades\Auth;

class OrderFeedbackController extends Controller
{
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        OrderFeedback::create([
            'order_id' => $orderId,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }

    public function index()
    {
        $orders = Order::with(['food', 'feedback'])->where('user_id', auth()->id())->get();
        return view('order', compact('orders'));
    }
}
