@extends('layouts.app')
@section('title', 'Booking Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="fas fa-file-invoice me-2 text-danger"></i>Booking #{{ $booking->booking_reference }}</h2>
    <div>
        {!! $booking->status_badge !!}
        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary ms-2"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row g-4">
    <!-- Booking Info -->
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white fw-bold"><i class="fas fa-bed me-2"></i>Room & Stay Details</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Room:</strong> {{ $booking->room->room_number }} ({{ ucfirst($booking->room->room_type) }})</p>
                        <p><strong>Floor:</strong> {{ $booking->room->floor ?? 'N/A' }}</p>
                        <p><strong>Check-In:</strong> {{ $booking->check_in_date->format('d M Y') }}</p>
                        <p><strong>Check-Out:</strong> {{ $booking->check_out_date->format('d M Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Nights:</strong> {{ $booking->nights }}</p>
                        <p><strong>Guests:</strong> {{ $booking->guests }}</p>
                        <p><strong>Rate/Night:</strong> ${{ number_format($booking->room->price_per_night,2) }}</p>
                        <p><strong>Total Amount:</strong> <span class="text-danger fw-bold fs-5">${{ number_format($booking->total_amount,2) }}</span></p>
                    </div>
                </div>
                @if($booking->special_requests)
                <div class="alert alert-info mt-2 mb-0"><strong>Special Requests:</strong> {{ $booking->special_requests }}</div>
                @endif
            </div>
        </div>

        <!-- Guest Info -->
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold"><i class="fas fa-users me-2"></i>Guest Information</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Name</th><th>ID Type</th><th>ID Number</th><th>Nationality</th><th>Phone</th><th>Email</th></tr></thead>
                    <tbody>
                        @foreach($booking->guests as $guest)
                        <tr>
                            <td>{{ $guest->full_name }} @if($guest->is_primary)<span class="badge bg-danger ms-1">Primary</span>@endif</td>
                            <td>{{ ucfirst(str_replace('_',' ',$guest->id_type)) }}</td>
                            <td>{{ $guest->id_number }}</td>
                            <td>{{ $guest->nationality }}</td>
                            <td>{{ $guest->phone }}</td>
                            <td>{{ $guest->email }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Status Management -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-dark text-white fw-bold"><i class="fas fa-cog me-2"></i>Update Status</div>
            <div class="card-body">
                <form action="{{ route('bookings.update-status',$booking) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">New Status</label>
                        <select name="status" class="form-select" required>
                            @foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $booking->status==$s?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3" id="cancelReasonDiv" style="display:none">
                        <label class="form-label">Cancellation Reason</label>
                        <textarea name="cancellation_reason" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Update Status</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold"><i class="fas fa-info-circle me-2"></i>Booking Summary</div>
            <div class="card-body">
                <p><strong>Booked By:</strong> {{ $booking->user->name }}</p>
                <p><strong>Booking Date:</strong> {{ $booking->created_at->format('d M Y H:i') }}</p>
                <p><strong>Payment:</strong> <span class="badge {{ $booking->payment_status=='paid'?'bg-success':'bg-warning' }}">{{ ucfirst($booking->payment_status) }}</span></p>
                <p><strong>Method:</strong> {{ ucfirst(str_replace('_',' ',$booking->payment_method ?? 'N/A')) }}</p>
                @if($booking->confirmed_at)
                <p><strong>Confirmed At:</strong> {{ $booking->confirmed_at->format('d M Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.querySelector('select[name="status"]').addEventListener('change', function(){
    document.getElementById('cancelReasonDiv').style.display = this.value === 'cancelled' ? 'block' : 'none';
});
</script>
@endpush
@endsection
