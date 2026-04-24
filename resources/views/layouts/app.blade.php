<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Prism Eyewear') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo/fav.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; color: #343a40; }
        .sidebar { min-height: 100vh; background-color: #ffffff; border-right: 1px solid #e9ecef; }
        .sidebar-link { color: #6c757d; font-weight: 500; padding: 10px 15px; display: block; text-decoration: none; border-radius: 8px; margin-bottom: 5px; transition: all 0.2s;}
        .sidebar-link:hover, .sidebar-link.active { background-color: #eff6ff; color: #0d6efd; }
        .sidebar-link i { margin-right: 10px; }
        .top-navbar { background-color: #ffffff; border-bottom: 1px solid #e9ecef; padding: 15px 20px; }
        .card { border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border-radius: 12px; }
        .card-header { background-color: #ffffff; border-bottom: 1px solid #f1f3f5; border-radius: 12px 12px 0 0 !important; font-weight: 600; padding: 1rem 1.25rem; }
        .btn-primary { background-color: #0d6efd; border: none; border-radius: 8px; padding: 8px 16px; font-weight: 500; }
        .btn-primary:hover { background-color: #0b5ed7; }
        .table > :not(caption) > * > * { padding: 1rem 0.75rem; border-color: #f1f3f5; color: #495057; }
        .table th { font-weight: 600; color: #6c757d; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .page-title { font-weight: 700; color: #212529; letter-spacing: -0.5px; }
        .stat-card { transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .badge { font-weight: 500; padding: 0.5em 0.8em; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3 d-none d-md-block" style="width: 260px;">
            <div class="d-flex align-items-center mb-4 mt-2 px-2">
                <img src="{{ asset('assets/img/logo/logo.jpg') }}" alt="Prism Eyewear" style="height: 100px !important; width: auto;">
            </div>
            <ul class="list-unstyled">
                <li><a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2"></i> Dashboard</a></li>
                <li class="mt-4 mb-2 px-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 600;">CRM</li>
                <li><a href="{{ route('customers.index') }}" class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Customers</a></li>
                <li><a href="{{ route('prescriptions.index') }}" class="sidebar-link {{ request()->routeIs('prescriptions.*') ? 'active' : '' }}"><i class="bi bi-file-medical"></i> Prescriptions</a></li>
                <li class="mt-4 mb-2 px-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 600;">Operations</li>
                <li><a href="{{ route('repairs.index') }}" class="sidebar-link {{ request()->routeIs('repairs.*') ? 'active' : '' }}"><i class="bi bi-tools"></i> Repairs</a></li>
                <li><a href="{{ route('orders.index') }}" class="sidebar-link {{ request()->routeIs('orders.*') ? 'active' : '' }}"><i class="bi bi-bag"></i> Orders</a></li>
                <li class="mt-4 mb-2 px-3 text-uppercase text-muted" style="font-size: 0.75rem; font-weight: 600;">Finance</li>
                <li><a href="{{ route('invoices.index') }}" class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}"><i class="bi bi-receipt"></i> Billing & Invoices</a></li>
                
                <li class="nav-title mt-4 mb-2 small text-muted fw-bold text-uppercase px-2 tracking-wide">Settings</li>
                <li><a href="{{ route('repair-types.index') }}" class="sidebar-link {{ request()->routeIs('repair-types.*') ? 'active' : '' }}"><i class="bi bi-tools"></i> Repair Types</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1" style="min-width: 0; background-color: #f9fbfd;">
            <!-- Top Navbar -->
            <div class="top-navbar d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center d-md-none">
                    <button class="btn btn-light me-2"><i class="bi bi-list"></i></button>
                    <img src="{{ asset('assets/img/logo/logo.jpg') }}" alt="Prism Eyewear" style="height: 30px; width: auto;">
                </div>
                <!-- Global Search -->
                <form action="{{ route('search') }}" method="GET" class="search-bar d-none d-md-flex align-items-center bg-light rounded-pill px-3 py-1 mb-0" style="width: 350px;">
                    <i class="bi bi-search text-muted me-2"></i>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control border-0 bg-transparent shadow-none" placeholder="Search customers, orders, repairs...">
                </form>
                <div class="d-flex align-items-center">
                    <button class="btn btn-light rounded-circle me-3"><i class="bi bi-bell"></i></button>
                    <div class="dropdown">
                        <button class="btn d-flex align-items-center gap-2 border-0 bg-transparent p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px; font-weight:600; font-size:14px; flex-shrink:0;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="ms-1 fw-medium d-none d-md-block text-dark" style="font-size:14px;">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down text-muted d-none d-md-block" style="font-size:11px;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius:12px; min-width:200px;">
                            <li class="px-3 pt-3 pb-2">
                                <div class="fw-semibold text-dark" style="font-size:13px;">{{ auth()->user()->name }}</div>
                                <div class="text-muted" style="font-size:12px;">{{ auth()->user()->email }}</div>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('profile.show') }}">
                                    <i class="bi bi-person-circle text-primary"></i> My Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="p-4 p-md-5">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
