<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerNoteController;
use App\Http\Controllers\CustomerDocumentController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $metrics = [
        'customers_count' => \App\Models\Customer::count(),
        'active_repairs' => \App\Models\Repair::whereNotIn('status', ['Completed', 'Collected'])->count(),
        'pending_orders' => \App\Models\Order::whereNotIn('order_status', ['Completed', 'Cancelled'])->count(),
        'monthly_revenue' => \App\Models\Invoice::whereMonth('invoice_date', date('m'))
                            ->whereYear('invoice_date', date('Y'))
                            ->sum('total_amount')
    ];
    
    $recentOrders = \App\Models\Order::with('customer')->orderBy('created_at', 'desc')->take(5)->get();
    
    return view('dashboard', compact('metrics', 'recentOrders'));
})->name('dashboard');

Route::put('customers/{customer}/comments', [CustomerController::class, 'updateComments'])->name('customers.updateComments');
Route::resource('customers', CustomerController::class);
Route::resource('prescriptions', PrescriptionController::class);
Route::resource('repairs', RepairController::class);
Route::resource('orders', OrderController::class);

Route::get('invoices/{invoice}/print/a4', [InvoiceController::class, 'printA4'])->name('invoices.print.a4');
Route::get('invoices/{invoice}/print/thermal', [InvoiceController::class, 'printThermal'])->name('invoices.print.thermal');
Route::resource('invoices', InvoiceController::class);

Route::post('customer-notes', [CustomerNoteController::class, 'store'])->name('customer-notes.store');
Route::post('customer-documents', [CustomerDocumentController::class, 'store'])->name('customer-documents.store');

Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');
Route::resource('repair-types', \App\Http\Controllers\RepairTypeController::class)->only(['index', 'store', 'destroy']);
