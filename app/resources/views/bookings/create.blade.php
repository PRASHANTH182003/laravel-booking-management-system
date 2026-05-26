@extends('layouts.app')
@section('title', 'New Booking')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="fas fa-plus-circle me-2 text-danger"></i>New Booking</h2>
    <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<form action="{{ route('bookings.store') }}" method="POST">
@csrf
<div class="row g-4">
    <!-- Booking Details -->
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold"><i class="fas fa-calendar me-2"></i>Booking Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Room <span class="text-danger">*</span></label>
                        <select name="room_id" class="form-select" required>
                            <option value="">-- Select Room --</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id')==$room->id?'selected':'' }}>
                                Room {{ $room->room_number }} — {{ ucfirst($room->room_type) }} | ${{ $room->price_per_night }}/night | Max {{ $room->capacity }} guests
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Check-In Date <span class="text-danger">*</span></label>
                        <input type="date" name="check_in_date" class="form-control" value="{{ old('check_in_date') }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Check-Out Date <span class="text-danger">*</span></label>
                        <input type="date" name="check_out_date" class="form-control" value="{{ old('check_out_date') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Number of Guests <span class="text-danger">*</span></label>
                        <input type="number" name="guests" class="form-control" value="{{ old('guests',1) }}" min="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Special Requests</label>
                        <textarea name="special_requests" class="form-control" rows="3" placeholder="Any special requirements...">{{ old('special_requests') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guest Information -->
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold"><i class="fas fa-user me-2"></i>Primary Guest Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="guest_name" class="form-control" value="{{ old('guest_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ID Type</label>
                        <select name="guest_id_type" class="form-select" required>
                            <option value="passport">Passport</option>
                            <option value="national_id">National ID</option>
                            <option value="driving_license">Driving License</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ID Number</label>
                        <input type="text" name="guest_id_number" class="form-control" value="{{ old('guest_id_number') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nationality</label>
                        <input type="text" name="guest_nationality" class="form-control" value="{{ old('guest_nationality') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="guest_phone" class="form-control" value="{{ old('guest_phone') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="guest_email" class="form-control" value="{{ old('guest_email') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-grid mt-3">
            <button type="submit" class="btn btn-danger btn-lg">
                <i class="fas fa-save me-2"></i>Create Booking
            </button>
        </div>
    </div>
</div>
</form>
@endsection
