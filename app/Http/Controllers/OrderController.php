<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;

use Illuminate\Http\Request;
use App\Models\Customer;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('customer');
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                  });
        }
        $orders = $query->latest('order_date')->paginate(15);
        return view('orders.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $customer_id = $request->get('customer_id');
        $customer = $customer_id ? Customer::find($customer_id) : null;
        return view('orders.create', compact('customer'));
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        
        $totalAmount = 0;
        $totalTax = 0;
        $totalDiscount = 0;

        foreach ($data['items'] as &$item) {
            $itemTotal = ($item['unit_price'] * $item['quantity']) - ($item['discount'] ?? 0) + ($item['tax'] ?? 0);
            $item['total'] = $itemTotal;

            $totalAmount += $itemTotal;
            $totalTax += ($item['tax'] ?? 0);
            $totalDiscount += ($item['discount'] ?? 0);
        }
        
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(substr(uniqid(), -6)),
            'customer_id' => $data['customer_id'],
            'order_date' => $data['order_date'],
            'order_status' => $data['order_status'] ?? 'Completed',
            'sales_staff' => $data['sales_staff'] ?? null,
            'total_amount' => $totalAmount,
            'tax_amount' => $totalTax,
            'discount_amount' => $totalDiscount,
        ]);

        $order->items()->createMany($data['items']);

        return redirect()->route('orders.show', $order)->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items', 'invoices']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load('items');
        return view('orders.edit', compact('order'));
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $data = $request->validated();
        
        $order->update([
            'order_date' => $data['order_date'],
            'order_status' => $data['order_status'] ?? 'Completed',
            'sales_staff' => $data['sales_staff'] ?? null,
        ]);

        $order->items()->delete();
        
        $totalAmount = 0;
        $totalTax = 0;
        $totalDiscount = 0;

        foreach ($data['items'] as &$item) {
            $itemTotal = ($item['unit_price'] * $item['quantity']) - ($item['discount'] ?? 0) + ($item['tax'] ?? 0);
            $item['total'] = $itemTotal;

            $totalAmount += $itemTotal;
            $totalTax += ($item['tax'] ?? 0);
            $totalDiscount += ($item['discount'] ?? 0);
        }

        $order->items()->createMany($data['items']);
        $order->update([
            'total_amount' => $totalAmount,
            'tax_amount' => $totalTax,
            'discount_amount' => $totalDiscount,
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }
}
