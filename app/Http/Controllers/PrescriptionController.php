<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;

use Illuminate\Http\Request;
use App\Models\Customer;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with('customer');
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('customer_number', 'like', "%{$search}%");
            });
        }
        $prescriptions = $query->latest('prescription_date')->paginate(15);
        return view('prescriptions.index', compact('prescriptions'));
    }

    public function create(Request $request)
    {
        $customer_id = $request->get('customer_id');
        $customer = $customer_id ? Customer::find($customer_id) : null;
        return view('prescriptions.create', compact('customer'));
    }

    public function store(StorePrescriptionRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $prescription = Prescription::create($data);

        return redirect()->route('customers.show', $prescription->customer_id)
                         ->with('success', 'Prescription added successfully.');
    }

    public function show(Prescription $prescription)
    {
        $prescription->load('customer');
        return view('prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        return view('prescriptions.edit', compact('prescription'));
    }

    public function update(UpdatePrescriptionRequest $request, Prescription $prescription)
    {
        $prescription->update($request->validated());

        return redirect()->route('customers.show', $prescription->customer_id)
                         ->with('success', 'Prescription updated successfully.');
    }

    public function destroy(Prescription $prescription)
    {
        $customer_id = $prescription->customer_id;
        $prescription->delete();
        return redirect()->route('customers.show', $customer_id)
                         ->with('success', 'Prescription deleted successfully.');
    }
}
