<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::where('category', 'Shop');
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('customer_number', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $customers = $query->latest()->paginate(15);
        return view('shops.index', compact('customers'));
    }

    public function create()
    {
        return view('shops.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();
        // Find all numeric customer numbers
        $numbers = \App\Models\Customer::pluck('customer_number')
            ->filter(fn($n) => is_numeric($n))
            ->map(fn($n) => intval($n));
        
        $nextCustomerId = $numbers->count() > 0 ? $numbers->max() + 1 : 100;
        $nextCustomerId = max(100, $nextCustomerId);
        
        // Final safety check: ensure the generated number doesn't exist (even if it's alphanumeric)
        while (\App\Models\Customer::where('customer_number', sprintf('%05d', $nextCustomerId))->exists()) {
            $nextCustomerId++;
        }
        
        $data['customer_number'] = sprintf('%05d', $nextCustomerId);
        $data['created_by'] = auth()->id();
        $data['category'] = 'Shop';

        $customer = Customer::create($data);

        return redirect()->route('shops.show', $customer)->with('success', 'Shop created successfully.');
    }

    public function show(Customer $shop) // model binding still Customer
    {
        $shop->load(['prescriptions', 'repairs', 'orders', 'invoices', 'notes.user', 'documents']);
        // Pass it to the view as customer to minimize view changes, but we will rename variable in view? 
        // Let's pass as 'customer' so view code works without renaming variables inside.
        return view('shops.show', ['customer' => $shop]);
    }

    public function edit(Customer $shop)
    {
        return view('shops.edit', ['customer' => $shop]);
    }

    public function update(UpdateCustomerRequest $request, Customer $shop)
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();
        $data['category'] = 'Shop';
        $shop->update($data);

        return redirect()->route('shops.show', $shop)->with('success', 'Shop updated successfully.');
    }

    public function updateComments(Request $request, Customer $shop)
    {
        $shop->cust_comms = $request->cust_comms;
        $shop->save();
        return redirect()->back()->with('success', 'Comments updated successfully.');
    }

    public function convertToCustomer(Customer $shop)
    {
        $shop->update(['category' => 'Customer']);
        return redirect()->route('customers.show', $shop)->with('success', 'Successfully moved to Customers.');
    }

    public function destroy(Customer $shop)
    {
        $shop->delete();
        return redirect()->route('shops.index')->with('success', 'Shop deleted successfully.');
    }
}
