<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hotel Booking System') - Grand Palace Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: #1a1a2e; min-height: 100vh; padding-top: 20px; }
        .sidebar .nav-link { color: #ccc; padding: 12px 20px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.1); border-left: 4px solid #e94560; }
        .sidebar-brand { color: #e94560; font-size: 1.4rem; font-weight: bold; padding: 15px 20px; }
        .stat-card { border-radius: 12px; border: none; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
        .badge { font-size: 0.8rem; }
        .table thead { background: #1a1a2e; color: white; }
        .btn-action { padding: 4px 10px; font-size: 0.8rem; }
        @media (max-width: 768px) { .sidebar { min-height: auto; } }
    </style>
    @stack('styles')
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar p-0">
            <div class="sidebar-brand">
                <i class="fas fa-hotel me-2"></i>Grand Palace
            </div>
            <nav class="nav flex-column mt-3">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="{{ route('bookings.index') }}" class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check me-2"></i>Bookings
                </a>
                <a href="{{ route('rooms.index') }}" class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                    <i class="fas fa-door-open me-2"></i>Rooms
                </a>
                <a href="{{ route('bookings.create') }}" class="nav-link">
                    <i class="fas fa-plus-circle me-2"></i>New Booking
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
