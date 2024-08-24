<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OrderStatusUpdate;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'pickup_location' => 'required|string|max:255',
            'delivery_location' => 'required|string|max:255',
            'size' => 'required|string|max:50',
            'weight' => 'required|string|max:50',
            'pickup_time' => 'required|date',
            'delivery_time' => 'required|date',
        ]);

        $order = Order::create([
            'user_id' => Auth::user()->id,
            'pickup_location' => $request->pickup_location,
            'delivery_location' => $request->delivery_location,
            'size' => $request->size,
            'weight' => $request->weight,
            'pickup_time' => $request->pickup_time,
            'delivery_time' => $request->delivery_time,
        ]);

        $admin = User::where('role', 'admin')->first();
        $admin->notify(new OrderStatusUpdate($order));
        return response()->json($order, 201);
    }

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->get();

        return response()->json($orders);
    }
}
