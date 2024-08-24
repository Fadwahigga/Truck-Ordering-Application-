<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OrderStatusUpdate;
use Illuminate\Notifications\DatabaseNotification;

class AdminController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')->get();
        $notifications = DatabaseNotification::where('notifiable_id', Auth::id())
            ->where('notifiable_type', get_class(Auth::user()))
            ->get();

        return view('admin.orders', ['orders' => $orders, 'notifications' => $notifications]);
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->input('status');
        $order->save();

        $order->user->notify(new OrderStatusUpdate($order));

        return redirect()->route('admin.orders')->with('success', 'Order status updated.');
    }

    public function getNewNotifications()
    {
        $notifications = DatabaseNotification::where('notifiable_id', Auth::id())
            ->where('notifiable_type', get_class(Auth::user()))
            ->whereNull('read_at')
            ->get();
        return response()->json($notifications);
    }

    public function markNotificationAsRead($orderId)
    {
        $notification = DatabaseNotification::where('notifiable_id', Auth::id())
            ->where('notifiable_type', get_class(Auth::user()))
            ->where('data->order_id', $orderId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }
}
