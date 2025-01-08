<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderItem;
use App\Models\Transaction;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        return view('user.index');
    }
    public function orders()
    {
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        return view('user.orders', compact('orders'));
    }

    public function order_details($order_id)
    {
        $order = Order::where('user_id', Auth::user()->id)
            ->where('id', $order_id)
            ->first();

        if ($order) {
            $orderItems = OrderItem::where('order_id', $order->id)
                ->orderBy('id')
                ->paginate(12);

            $transaction = Transaction::where('order_id', $order->id)
                ->first();

            return view('user.order-details', compact('order', 'orderItems', 'transaction'));
        } else {
            return redirect()->route('login');
        }
    }

    public function order_cancel(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = "canceled";
        $order->canceled_date = Carbon::now();
        $order->save();
        return back()->with('status', "Order has been cancelled successfully!");
    }


    public function editOrder($order_id)
    {
        $order = Order::findOrFail($order_id); // Replace with your model name
        return view('user.order-edit', compact('order'));
    }

    public function updateOrder(Request $request, $order_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'subtotal' => 'required|numeric|min:0',
            'status' => 'required|string|in:ordered,delivered,canceled',
            'note' => 'nullable|string',
        ]);



        $order = Order::findOrFail($order_id);
        $order->update($request->all());
        $order->save();


        return redirect()->back()->with('success', 'Order updated successfully.');
    }
}
