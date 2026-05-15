<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\QuoteController;
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
    Route::put('customers/{customer}/convert', [CustomerController::class, 'convertToShop'])->name('customers.convert');
    Route::resource('customers', CustomerController::class);
    
    Route::put('shops/{shop}/comments', [\App\Http\Controllers\ShopController::class, 'updateComments'])->name('shops.updateComments');
    Route::put('shops/{shop}/convert', [\App\Http\Controllers\ShopController::class, 'convertToCustomer'])->name('shops.convert');
    Route::resource('shops', \App\Http\Controllers\ShopController::class);

    Route::resource('prescriptions', PrescriptionController::class);

    // Operations
    Route::resource('repairs', RepairController::class);
    Route::resource('orders', OrderController::class);

    // Finance
    Route::get('quotes/{quote}/print/a4', [QuoteController::class, 'printA4'])->name('quotes.print.a4');
    Route::resource('quotes', QuoteController::class);

    Route::get('invoices/{invoice}/print/a4',      [InvoiceController::class, 'printA4'])->name('invoices.print.a4');
    Route::get('invoices/{invoice}/print/thermal', [InvoiceController::class, 'printThermal'])->name('invoices.print.thermal');
    Route::resource('invoices', InvoiceController::class);
    Route::put('/invoices/{invoice}/payment', [InvoiceController::class, 'updatePayment'])->name('invoices.updatePayment');

    // Reports Module
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [App\Http\Controllers\ReportController::class, 'printSelected'])->name('reports.print');
    Route::get('/reports/customer/{customer}', [App\Http\Controllers\ReportController::class, 'customer'])->name('reports.customer');

    // Report Downloads (CSV)
    Route::get('/reports/download/invoice/{invoice}', [App\Http\Controllers\ReportDownloadController::class, 'downloadSingle'])->name('reports.download.single');
    Route::get('/reports/download/bulk', [App\Http\Controllers\ReportDownloadController::class, 'downloadBulk'])->name('reports.download.bulk');
    Route::get('/reports/download/customer/{customer}/all', [App\Http\Controllers\ReportDownloadController::class, 'downloadCustomerAll'])->name('reports.download.customer.all');

    // Misc
    Route::post('customer-notes',     [CustomerNoteController::class, 'store'])->name('customer-notes.store');
    Route::post('customer-documents', [CustomerDocumentController::class, 'store'])->name('customer-documents.store');

    Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');
    Route::resource('repair-types', \App\Http\Controllers\RepairTypeController::class)->only(['index', 'store', 'destroy']);
    Route::resource('prescription-types', \App\Http\Controllers\PrescriptionTypeController::class)->only(['index', 'store', 'destroy']);

    // QZ Tray — serve certificate (via route, not static file, to bypass ad blockers)
    Route::get('/qz-cert', function () {
        $cert = file_get_contents(public_path('digital-certificate.txt'));
        return response($cert, 200)->header('Content-Type', 'text/plain');
    })->name('qz.cert');

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
