@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="fas fa-tachometer-alt me-2 text-danger"></i>Dashboard</h2>
    <span class="text-muted">{{ now()->format('l, d M Y') }}</span>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card text-white" style="background: linear-gradient(135deg,#667eea,#764ba2)">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75">Total Bookings</p>
                    <h3 class="fw-bold mb-0">{{ $stats['total_bookings'] }}</h3>
                </div>
                <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card text-white" style="background: linear-gradient(135deg,#f093fb,#f5576c)">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75">Pending</p>
                    <h3 class="fw-bold mb-0">{{ $stats['pending'] }}</h3>
                </div>
                <i class="fas fa-clock fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card text-white" style="background: linear-gradient(135deg,#4facfe,#00f2fe)">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75">Confirmed</p>
                    <h3 class="fw-bold mb-0">{{ $stats['confirmed'] }}</h3>
                </div>
                <i class="fas fa-check-circle fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card text-white" style="background: linear-gradient(135deg,#43e97b,#38f9d7)">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1 opacity-75">Total Revenue</p>
                    <h3 class="fw-bold mb-0">${{ number_format($stats['total_revenue'],2) }}</h3>
                </div>
                <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Room Status & Recent Bookings -->
<div class="row g-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-header bg-white fw-bold"><i class="fas fa-door-open me-2 text-primary"></i>Room Status</div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span><i class="fas fa-circle text-success me-2"></i>Available</span>
                    <strong>{{ $stats['available_rooms'] }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span><i class="fas fa-circle text-danger me-2"></i>Occupied</span>
                    <strong>{{ $stats['occupied_rooms'] }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span><i class="fas fa-circle text-warning me-2"></i>Checked In</span>
                    <strong>{{ $stats['checked_in'] }}</strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card stat-card">
            <div class="card-header bg-white fw-bold d-flex justify-content-between">
                <span><i class="fas fa-history me-2 text-danger"></i>Recent Bookings</span>
                <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-danger">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>Reference</th><th>Guest</th><th>Room</th><th>Check-In</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($recentBookings as $booking)
                        <tr>
                            <td><a href="{{ route('bookings.show', $booking) }}" class="text-decoration-none fw-bold">{{ $booking->booking_reference }}</a></td>
                            <td>{{ $booking->user->name }}</td>
                            <td>{{ $booking->room->room_number }}</td>
                            <td>{{ $booking->check_in_date->format('d M Y') }}</td>
                            <td>{!! $booking->status_badge !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
