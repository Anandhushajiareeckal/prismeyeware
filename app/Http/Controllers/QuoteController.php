<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Repair;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::with('customer');

        if ($request->filled('quote_number')) {
            $query->where('quote_number', 'like', "%" . $request->quote_number . "%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('quote_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('quote_date', '<=', $request->date_to);
        }

        if ($request->filled('customer_name')) {
            $search = $request->customer_name;
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $quotes = $query->latest('quote_date')->paginate(15)->withQueryString();

        return view('quotes.index', compact('quotes'));
    }

    public function create(Request $request)
    {
        $customer_id = $request->get('customer_id');
        $customer = $customer_id ? Customer::find($customer_id) : null;
        $repair = $request->get('repair_id') ? Repair::with('items')->find($request->get('repair_id')) : null;
        $order = $request->get('order_id') ? Order::with('items')->find($request->get('order_id')) : null;
        return view('quotes.create', compact('customer', 'repair', 'order'));
    }

    public function store(StoreQuoteRequest $request)
    {
        $data = $request->validated();
        
        $subtotal = 0;
        $totalTax = 0;
        $totalDiscount = 0;
        
        foreach ($data['items'] as &$item) {
            $itemSubtotal = $item['rate'] * $item['quantity'];
            // Inclusive tax calculation: Tax = Total - (Total / 1.15)
            $item['tax'] = ($itemSubtotal - ($item['discount'] ?? 0)) - (($itemSubtotal - ($item['discount'] ?? 0)) / 1.15);
            
            $item['total'] = $itemSubtotal - ($item['discount'] ?? 0);

            $subtotal += ($item['rate'] * $item['quantity']);
            $totalTax += ($item['tax'] ?? 0);
            $totalDiscount += ($item['discount'] ?? 0);
        }
        
        $totalAmount = $subtotal - $totalDiscount;

        $quote = Quote::create([
            'quote_number' => str_pad((Quote::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT),
            'customer_id' => $data['customer_id'],
            'order_id' => $data['order_id'] ?? null,
            'repair_id' => $data['repair_id'] ?? null,
            'quote_date' => $data['quote_date'],
            'subtotal' => $subtotal,
            'tax_amount' => $totalTax,
            'discount_amount' => $totalDiscount,
            'total_amount' => $totalAmount,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'staff_name' => $data['staff_name'] ?? null,
        ]);

        $quote->items()->createMany($data['items']);

        return redirect()->route('quotes.show', $quote)->with('success', 'Quote created successfully.');
    }

    public function show(Quote $quote)
    {
        $quote->load(['customer', 'items']);
        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $quote->load('items');
        return view('quotes.edit', compact('quote'));
    }

    public function update(UpdateQuoteRequest $request, Quote $quote)
    {
        $data = $request->validated();

        $subtotal = 0;
        $totalTax = 0;
        $totalDiscount = 0;
        
        $quote->items()->delete();

        foreach ($data['items'] as &$item) {
            $itemSubtotal = $item['rate'] * $item['quantity'];
            // Inclusive tax calculation: Tax = Total - (Total / 1.15)
            $item['tax'] = ($itemSubtotal - ($item['discount'] ?? 0)) - (($itemSubtotal - ($item['discount'] ?? 0)) / 1.15);
            
            $item['total'] = $itemSubtotal - ($item['discount'] ?? 0);

            $subtotal += ($item['rate'] * $item['quantity']);
            $totalTax += ($item['tax'] ?? 0);
            $totalDiscount += ($item['discount'] ?? 0);
        }
        
        $totalAmount = $subtotal - $totalDiscount;

        $quote->update([
            'quote_date' => $data['quote_date'],
            'subtotal' => $subtotal,
            'tax_amount' => $totalTax,
            'discount_amount' => $totalDiscount,
            'total_amount' => $totalAmount,
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
            'staff_name' => $data['staff_name'] ?? null,
        ]);

        $quote->items()->createMany($data['items']);

        return redirect()->route('quotes.show', $quote)->with('success', 'Quote updated successfully.');
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Quote deleted successfully.');
    }

    public function printA4(Quote $quote)
    {
        $quote->load(['customer', 'items']);
        return view('quotes.print_a4', compact('quote'));
    }
}
