<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;



class OrderController extends Controller
{
    public function index()
    {
        {
            $orders = Order::select('orders.id', 'orders.created_at', 'orders.status', 'orders.total_amount', 'products.name as product_name', 'users.name as user_name')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->groupBy('orders.id', 'orders.created_at', 'orders.status', 'orders.total_amount', 'products.name', 'users.name')
                ->get();
    
            return response()->json($orders, 200);
        }
    
    }

    public function store(Request $request)
    {
      

        $order = Order::create([
            'user_id' => $request->user_id,
            'total_amount' => $request->total_amount,
            'status' => 'pending',
        ]);

        foreach ($request->order_items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return response()->json($order->load('orderItems'), 201);
    }

    public function show($id)
    {
        $order = Order::with('orderItems')->findOrFail($id);
        return response()->json($order, 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return response()->json($order, 200);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(null, 204);
    }

    public function getCards()
    {
        $aggregates = DB::table('orders')
            ->selectRaw('
                SUM(total_amount) as total_orders,
                SUM(CASE WHEN status = "pending" THEN total_amount ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = "completed" THEN total_amount ELSE 0 END) as completed_orders
            ')
            ->first();

        // Get total distinct users count
        $totalUsers = User::distinct('id')->count('id');

        return response()->json([
            'total_orders' => $aggregates->total_orders,
            'pending_orders' => $aggregates->pending_orders,
            'completed_orders' => $aggregates->completed_orders,
            'total_users' => $totalUsers,
        ], 200);
    }
}
