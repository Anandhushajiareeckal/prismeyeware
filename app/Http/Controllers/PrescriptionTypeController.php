<?php

namespace App\Http\Controllers;

use App\Models\PrescriptionType;
use Illuminate\Http\Request;

class PrescriptionTypeController extends Controller
{
    public function index()
    {
        $prescriptionTypes = PrescriptionType::orderBy('name')->get();
        return view('prescription_types.index', compact('prescriptionTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:prescription_types,name',
            'status' => 'nullable|string|in:Active,Inactive',
        ]);

        PrescriptionType::create([
            'name' => $request->name,
            'status' => $request->status ?? 'Active',
        ]);

        return redirect()->route('prescription-types.index')->with('success', 'Prescription type added successfully.');
    }

    public function destroy(PrescriptionType $prescriptionType)
    {
        $prescriptionType->delete();
        return redirect()->route('prescription-types.index')->with('success', 'Prescription type deleted successfully.');
    }
}
