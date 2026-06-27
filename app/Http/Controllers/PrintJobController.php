<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PrintJob;

class PrintJobController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'payload' => 'required|array',
        ]);

        $job = PrintJob::create([
            'payload' => $request->payload,
            'status' => 'pending'
        ]);

        return response()->json(['success' => true, 'job_id' => $job->id]);
    }

    public function pending()
    {
        $jobs = PrintJob::where('status', 'pending')
                    ->orderBy('created_at', 'asc')
                    ->get();
        return response()->json($jobs);
    }

    public function markPrinted($id)
    {
        $job = PrintJob::findOrFail($id);
        $job->update(['status' => 'printed']);
        return response()->json(['success' => true]);
    }
}
