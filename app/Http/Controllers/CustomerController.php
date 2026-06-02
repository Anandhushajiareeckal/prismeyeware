<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::where('category', '!=', 'Shop'); // or 'Customer' or default null
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
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
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
        $data['category'] = 'Customer';

        $customer = Customer::create($data);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['prescriptions', 'repairs', 'orders', 'invoices', 'notes.user', 'documents']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();
        $data['updated_by'] = auth()->id();
        $data['category'] = 'Customer';
        $customer->update($data);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated successfully.');
    }

    public function updateComments(Request $request, Customer $customer)
    {
        $customer->cust_comms = $request->cust_comms;
        $customer->save();
        return redirect()->back()->with('success', 'Comments updated successfully.');
    }

    public function convertToShop(Customer $customer)
    {
        $customer->update(['category' => 'Shop']);
        return redirect()->route('shops.show', $customer)->with('success', 'Successfully moved to Shops.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
