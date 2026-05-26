<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of all bookings.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'room'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('check_in_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('check_out_date', '<=', $request->to_date);
        }

        // Search by reference or guest name
        if ($request->filled('search')) {
            $query->where('booking_reference', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
        }

        $bookings = $query->paginate(15);

        $stats = [
            'total'       => Booking::count(),
            'pending'     => Booking::where('status', 'pending')->count(),
            'confirmed'   => Booking::where('status', 'confirmed')->count(),
            'checked_in'  => Booking::where('status', 'checked_in')->count(),
            'cancelled'   => Booking::where('status', 'cancelled')->count(),
        ];

        return view('bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $rooms = Room::available()->get();
        return view('bookings.create', compact('rooms'));
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'check_in_date'    => 'required|date|after_or_equal:today',
            'check_out_date'   => 'required|date|after:check_in_date',
            'guests'           => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:500',
            'payment_method'   => 'required|in:credit_card,debit_card,cash,bank_transfer',
            // Primary guest
            'guest_name'       => 'required|string|max:100',
            'guest_id_type'    => 'required|string',
            'guest_id_number'  => 'required|string|max:50',
            'guest_nationality'=> 'required|string|max:50',
            'guest_phone'      => 'required|string|max:20',
            'guest_email'      => 'required|email|max:100',
        ]);

        $room = Room::findOrFail($request->room_id);

        // Check availability
        if (!$room->isAvailableForDates($request->check_in_date, $request->check_out_date)) {
            return back()->withErrors(['room_id' => 'Room is not available for selected dates.'])->withInput();
        }

        $checkIn  = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        $nights   = $checkIn->diffInDays($checkOut);
        $total    = $room->price_per_night * $nights;

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'user_id'          => Auth::id(),
                'room_id'          => $room->id,
                'check_in_date'    => $request->check_in_date,
                'check_out_date'   => $request->check_out_date,
                'guests'           => $request->guests,
                'total_amount'     => $total,
                'status'           => 'pending',
                'special_requests' => $request->special_requests,
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'unpaid',
            ]);

            Guest::create([
                'booking_id'  => $booking->id,
                'full_name'   => $request->guest_name,
                'id_type'     => $request->guest_id_type,
                'id_number'   => $request->guest_id_number,
                'nationality' => $request->guest_nationality,
                'phone'       => $request->guest_phone,
                'email'       => $request->guest_email,
                'is_primary'  => true,
            ]);

            DB::commit();

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking created successfully! Reference: ' . $booking->booking_reference);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create booking. Please try again.'])->withInput();
        }
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'room', 'guests']);
        return view('bookings.show', compact('booking'));
    }

    /**
     * Update booking status.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'confirmed') {
            $data['confirmed_at'] = now();
            $data['payment_status'] = 'paid';
            $booking->room->update(['status' => 'occupied']);
        }

        if ($request->status === 'cancelled') {
            $request->validate(['cancellation_reason' => 'required|string']);
            $data['cancelled_at'] = now();
            $data['cancellation_reason'] = $request->cancellation_reason;
            $booking->room->update(['status' => 'available']);
        }

        if ($request->status === 'checked_out') {
            $booking->room->update(['status' => 'available']);
        }

        $booking->update($data);

        return back()->with('success', 'Booking status updated to ' . ucfirst($request->status));
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    /**
     * Dashboard with analytics.
     */
    public function dashboard()
    {
        $stats = [
            'total_bookings'   => Booking::count(),
            'pending'          => Booking::where('status', 'pending')->count(),
            'confirmed'        => Booking::where('status', 'confirmed')->count(),
            'checked_in'       => Booking::where('status', 'checked_in')->count(),
            'cancelled'        => Booking::where('status', 'cancelled')->count(),
            'total_revenue'    => Booking::whereIn('status', ['confirmed','checked_out'])->sum('total_amount'),
            'available_rooms'  => Room::where('status', 'available')->count(),
            'occupied_rooms'   => Room::where('status', 'occupied')->count(),
        ];

        $recentBookings = Booking::with(['user', 'room'])
            ->orderBy('created_at', 'desc')
            ->take(5)->get();

        $monthlyRevenue = Booking::whereIn('status', ['confirmed', 'checked_out'])
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('revenue', 'month');

        return view('dashboard', compact('stats', 'recentBookings', 'monthlyRevenue'));
    }
}
