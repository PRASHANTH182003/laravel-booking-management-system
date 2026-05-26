<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::withCount('bookings');

        if ($request->filled('type')) {
            $query->where('room_type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rooms = $query->paginate(12);
        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_number'    => 'required|string|unique:rooms',
            'room_type'      => 'required|in:single,double,suite,deluxe,family',
            'description'    => 'nullable|string',
            'price_per_night'=> 'required|numeric|min:1',
            'capacity'       => 'required|integer|min:1',
            'floor'          => 'nullable|string',
            'amenities'      => 'nullable|array',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }

        Room::create($data);

        return redirect()->route('rooms.index')
            ->with('success', 'Room added successfully.');
    }

    public function show(Room $room)
    {
        $room->load(['bookings.user']);
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'room_number'     => 'required|string|unique:rooms,room_number,' . $room->id,
            'room_type'       => 'required|in:single,double,suite,deluxe,family',
            'description'     => 'nullable|string',
            'price_per_night' => 'required|numeric|min:1',
            'capacity'        => 'required|integer|min:1',
            'floor'           => 'nullable|string',
            'amenities'       => 'nullable|array',
        ]);

        $room->update($request->except('image', '_token', '_method'));

        return redirect()->route('rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')
            ->with('success', 'Room deleted successfully.');
    }

    /**
     * Check room availability via AJAX.
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'check_in_date'  => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_type'      => 'nullable|string',
        ]);

        $availableRooms = Room::available()
            ->when($request->room_type, fn($q) => $q->where('room_type', $request->room_type))
            ->get()
            ->filter(fn($room) => $room->isAvailableForDates(
                $request->check_in_date,
                $request->check_out_date
            ));

        return response()->json($availableRooms);
    }
}
