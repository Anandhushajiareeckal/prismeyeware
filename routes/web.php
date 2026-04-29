<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerNoteController;
use App\Http\Controllers\CustomerDocumentController;

// ─── Guest Routes (Login) ───────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ─── Authenticated Routes ───────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile',  [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile',  [ProfileController::class, 'update'])->name('profile.update');

    // Dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', function () {
        $metrics = [
            'customers_count' => \App\Models\Customer::count(),
            'active_repairs'  => \App\Models\Repair::whereNotIn('status', ['Completed', 'Collected'])->count(),
            'pending_orders'  => \App\Models\Order::whereNotIn('order_status', ['Completed', 'Cancelled'])->count(),
            'monthly_revenue' => \App\Models\Invoice::whereMonth('invoice_date', date('m'))
                                    ->whereYear('invoice_date', date('Y'))
                                    ->sum('total_amount')
        ];

        $recentOrders = \App\Models\Order::with('customer')->orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard', compact('metrics', 'recentOrders'));
    })->name('dashboard');

    // CRM
    Route::put('customers/{customer}/comments', [CustomerController::class, 'updateComments'])->name('customers.updateComments');
    Route::resource('customers', CustomerController::class);
    Route::resource('prescriptions', PrescriptionController::class);

    // Operations
    Route::resource('repairs', RepairController::class);
    Route::resource('orders', OrderController::class);

    // Finance
    Route::get('invoices/{invoice}/print/a4',      [InvoiceController::class, 'printA4'])->name('invoices.print.a4');
    Route::get('invoices/{invoice}/print/thermal', [InvoiceController::class, 'printThermal'])->name('invoices.print.thermal');
    Route::resource('invoices', InvoiceController::class);

    // Misc
    Route::post('customer-notes',     [CustomerNoteController::class, 'store'])->name('customer-notes.store');
    Route::post('customer-documents', [CustomerDocumentController::class, 'store'])->name('customer-documents.store');

    Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');
    Route::resource('repair-types', \App\Http\Controllers\RepairTypeController::class)->only(['index', 'store', 'destroy']);

    // QZ Tray — sign print requests with private key (removes "Untrusted website" popup)
    Route::post('/qz-sign', function (\Illuminate\Http\Request $request) {
        $keyPath = storage_path('app/private-key.pem');
        if (!file_exists($keyPath)) {
            return response('Private key not found.', 500);
        }
        $privateKey = openssl_pkey_get_private(file_get_contents($keyPath));
        openssl_sign($request->input('request', ''), $signature, $privateKey, OPENSSL_ALGO_SHA512);
        return base64_encode($signature);
    })->name('qz.sign');
});
