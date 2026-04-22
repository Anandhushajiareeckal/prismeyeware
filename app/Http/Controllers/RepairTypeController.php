<?php

namespace App\Http\Controllers;

use App\Models\RepairType;
use Illuminate\Http\Request;

class RepairTypeController extends Controller
{
    public function index()
    {
        $repairTypes = RepairType::orderBy('name')->get();
        return view('repair_types.index', compact('repairTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:repair_types,name',
            'status' => 'nullable|string|in:Active,Inactive',
        ]);

        RepairType::create([
            'name' => $request->name,
            'status' => $request->status ?? 'Active',
        ]);

        return redirect()->route('repair-types.index')->with('success', 'Repair type added successfully.');
    }

    public function destroy(RepairType $repairType)
    {
        $repairType->delete();
        return redirect()->route('repair-types.index')->with('success', 'Repair type deleted successfully.');
    }
}
