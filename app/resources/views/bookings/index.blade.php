@extends('layouts.app')
@section('title', 'Manage Bookings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="fas fa-calendar-check me-2 text-danger"></i>Bookings Management</h2>
    <a href="{{ route('bookings.create') }}" class="btn btn-danger">
        <i class="fas fa-plus me-1"></i>New Booking
    </a>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    @foreach(['pending'=>['warning','clock'],'confirmed'=>['success','check-circle'],'checked_in'=>['primary','sign-in-alt'],'cancelled'=>['danger','times-circle']] as $s=>[$color,$icon])
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-{{ $icon }} fa-2x text-{{ $color }} mb-2"></i>
                <h4 class="fw-bold mb-0">{{ $stats[$s] }}</h4>
                <small class="text-muted text-capitalize">{{ str_replace('_',' ',$s) }}</small>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search reference / guest...">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-danger me-2"><i class="fas fa-filter me-1"></i>Filter</button>
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Reference</th><th>Guest</th><th>Room</th><th>Check-In</th>
                    <th>Check-Out</th><th>Nights</th><th>Amount</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td class="fw-bold"><a href="{{ route('bookings.show',$booking) }}" class="text-danger text-decoration-none">{{ $booking->booking_reference }}</a></td>
                    <td>{{ $booking->user->name }}</td>
                    <td>{{ $booking->room->room_number }} <small class="text-muted">({{ ucfirst($booking->room->room_type) }})</small></td>
                    <td>{{ $booking->check_in_date->format('d M Y') }}</td>
                    <td>{{ $booking->check_out_date->format('d M Y') }}</td>
                    <td>{{ $booking->nights }}</td>
                    <td>${{ number_format($booking->total_amount,2) }}</td>
                    <td>{!! $booking->status_badge !!}</td>
                    <td>
                        <a href="{{ route('bookings.show',$booking) }}" class="btn btn-sm btn-outline-primary btn-action">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form action="{{ route('bookings.destroy',$booking) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this booking?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger btn-action"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $bookings->withQueryString()->links() }}</div>
</div>
@endsection
