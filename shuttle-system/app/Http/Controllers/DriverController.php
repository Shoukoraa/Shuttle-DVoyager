<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function mySchedules()
    {
        $driver = \App\Models\Driver::where('user_id', auth()->id())->first();
        if (!$driver) {
            return response()->json([]);
        }
        
        $schedules = \App\Models\Schedule::where('driver_id', $driver->id)
            ->with(['locations', 'route.origin', 'route.destination', 'vehicle', 'bookings' => function($q) {
                $q->whereIn('status', ['paid', 'booked']);
            }])
            ->orderBy('departure_time', 'asc')
            ->get();
            
        $schedules->each(function($schedule) {
            $schedule->total_passengers = $schedule->bookings->sum('total_seat');
            unset($schedule->bookings); // Hide bookings details from this endpoint to save payload
        });
            
        return response()->json($schedules);
    }

    public function updateLocation(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $location = \App\Models\ScheduleLocation::create([
            'schedule_id' => $validated['schedule_id'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'recorded_at' => now()
        ]);

        return response()->json($location);
    }

    public function manifest($id)
    {
        $driver = \App\Models\Driver::where('user_id', auth()->id())->first();
        if (!$driver) {
            return response()->json(['message' => 'Not a driver'], 403);
        }

        $schedule = \App\Models\Schedule::where('id', $id)->where('driver_id', $driver->id)->first();
        if (!$schedule) {
            return response()->json(['message' => 'Schedule not found or not assigned to you'], 404);
        }

        $bookings = \App\Models\Booking::where('schedule_id', $id)
            ->whereIn('status', ['paid', 'booked']) // included booked for testing flexibility
            ->with(['customer.user', 'seats'])
            ->get();
            
        $manifest = $bookings->map(function ($booking) {
            return [
                'booking_id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'passenger_name' => $booking->passenger_name ?? $booking->customer->user->name ?? 'Unknown',
                'phone' => $booking->passenger_phone ?? $booking->customer->user->phone ?? '-',
                'seats' => $booking->seats->pluck('seat_number'),
                'status' => $booking->status
            ];
        });

        return response()->json($manifest);
    }
}
