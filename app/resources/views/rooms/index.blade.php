@extends('layouts.app')
@section('title', 'Rooms Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="fas fa-door-open me-2 text-danger"></i>Rooms Management</h2>
    <a href="{{ route('rooms.create') }}" class="btn btn-danger"><i class="fas fa-plus me-1"></i>Add Room</a>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach(['single','double','suite','deluxe','family'] as $t)
                    <option value="{{ $t }}" {{ request('type')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach(['available','occupied','maintenance'] as $s)
                    <option value="{{ $s }}" {{ request('status')==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-danger"><i class="fas fa-filter me-1"></i>Filter</button>
                <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary ms-2">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    @forelse($rooms as $room)
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="fw-bold mb-0">Room {{ $room->room_number }}</h5>
                    <span class="badge {{ $room->status=='available'?'bg-success':($room->status=='occupied'?'bg-danger':'bg-warning') }}">
                        {{ ucfirst($room->status) }}
                    </span>
                </div>
                <p class="text-muted mb-1"><i class="fas fa-tag me-1"></i>{{ ucfirst($room->room_type) }}</p>
                <p class="text-muted mb-1"><i class="fas fa-layer-group me-1"></i>Floor {{ $room->floor ?? 'N/A' }}</p>
                <p class="text-muted mb-1"><i class="fas fa-users me-1"></i>Capacity: {{ $room->capacity }}</p>
                <p class="text-danger fw-bold fs-5 mb-2">${{ number_format($room->price_per_night,2) }}<small class="text-muted fs-6">/night</small></p>
                @if($room->amenities)
                <div class="mb-2">
                    @foreach($room->amenities as $a)
                    <span class="badge bg-light text-dark me-1">{{ $a }}</span>
                    @endforeach
                </div>
                @endif
                <small class="text-muted">{{ $room->bookings_count }} total bookings</small>
            </div>
            <div class="card-footer bg-white d-flex gap-2">
                <a href="{{ route('rooms.show',$room) }}" class="btn btn-sm btn-outline-primary flex-fill"><i class="fas fa-eye me-1"></i>View</a>
                <a href="{{ route('rooms.edit',$room) }}" class="btn btn-sm btn-outline-warning flex-fill"><i class="fas fa-edit me-1"></i>Edit</a>
                <form action="{{ route('rooms.destroy',$room) }}" method="POST" onsubmit="return confirm('Delete room?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12"><p class="text-center text-muted">No rooms found.</p></div>
    @endforelse
</div>
<div class="mt-4">{{ $rooms->withQueryString()->links() }}</div>
@endsection
