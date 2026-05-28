<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Seat;
use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Trip;
use App\Services\PaymentStatusService;

class BookingController extends Controller
{
    public function myBookings(PaymentStatusService $paymentStatus)
    {
        $customer = \App\Models\Customer::where('user_id', auth()->id())->first();
        if (!$customer) {
            return response()->json([]);
        }
        
        $bookings = Booking::where('customer_id', $customer->id)
            ->with([
                'schedule.route.origin', 
                'schedule.route.destination', 
                'schedule.driver.user', 
                'schedule.driver.vehicle', 
                'seats', 
                'review', 
                'payment', 
                'schedule.locations' => function($q) {
                    $q->latest('recorded_at');
                }
            ])
            ->orderBy('booking_time', 'desc')
            ->get();

        $bookings->each(function ($booking) {
            $scheduleStatus = strtolower((string) optional($booking->schedule)->status);
            $bookingStatus = strtolower((string) $booking->status);

            if ($scheduleStatus === 'completed' && !in_array($bookingStatus, ['completed', 'cancelled'], true)) {
                $booking->update(['status' => 'completed']);
                $booking->status = 'completed';
            }
        });

        // We avoid calling syncBookingPayment synchronously inside the list API to prevent blocking external API requests that cause page load hangs.
        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seats' => 'required|array',
            'passenger_name' => 'nullable|string|max:255',
            'passenger_phone' => 'nullable|string|max:20',
            'passenger_email' => 'nullable|email|max:255',
        ]);

        return DB::transaction(function () use ($request) {

            // Cari kursi berdasarkan nomor kursi (bukan ID) untuk jadwal ini
            $seats = Seat::where('schedule_id', $request->schedule_id)
                ->whereIn('seat_number', $request->seats)
                ->lockForUpdate()
                ->get();

            if ($seats->count() !== count($request->seats)) {
                abort(400, "Beberapa kursi tidak valid atau tidak ditemukan di jadwal ini.");
            }

            foreach ($seats as $seat) {
                if ($seat->status === 'booked') {
                    abort(400, "Seat {$seat->seat_number} sudah dibooking");
                }
            }

            $customer = auth()->user()->customer;
            if (!$customer) {
                abort(403, "Hanya customer yang bisa mem-booking.");
            }

            // Get schedule with price
            $schedule = \App\Models\Schedule::find($request->schedule_id);
            if (!$schedule || !$schedule->price) {
                abort(400, "Jadwal tidak memiliki harga yang valid.");
            }

            // Calculate totals
            $pricePerSeat = $schedule->price;
            $serviceFee = (float) config('dvoyager.service_fee', 2500);
            $totalPrice = ($pricePerSeat * count($seats)) + $serviceFee;

            $booking = Booking::create([
                'customer_id' => $customer->id,
                'schedule_id' => $request->schedule_id,
                'booking_time' => now(),
                'total_seat' => count($seats),
                'passenger_name' => $request->passenger_name ?? $customer->user->name,
                'passenger_phone' => $request->passenger_phone ?? $customer->user->phone,
                'passenger_email' => $request->passenger_email ?? $customer->user->email,
                'price_per_seat' => $pricePerSeat,
                'total_price' => $totalPrice,
                'service_fee' => $serviceFee,
                'status' => 'booked'
            ]);

            foreach ($seats as $seat) {
                BookingSeat::create([
                    'booking_id' => $booking->id,
                    'seat_id' => $seat->id
                ]);

                $seat->update([
                    'status' => 'booked'
                ]);
            }

            return response()->json([
                'message' => 'Booking berhasil',
                'booking_id' => $booking->id,
                'price_per_seat' => $pricePerSeat,
                'total_price' => $totalPrice,
                'service_fee' => $serviceFee,
                'total_seat' => count($seats)
            ]);
        });
    }

    public function cancel($id)
    {
        return DB::transaction(function () use ($id) {
            $booking = Booking::where('id', $id)
                ->where('customer_id', auth()->user()->customer->id ?? 0)
                ->lockForUpdate()
                ->firstOrFail();

            if ($booking->status !== 'booked') {
                return response()->json(['message' => 'Cannot cancel this booking'], 400);
            }

            $booking->update(['status' => 'cancelled']);

            // Free up the seats
            foreach ($booking->seats as $seat) {
                $seat->update(['status' => 'available']);
            }

            return response()->json(['message' => 'Booking cancelled successfully']);
        });
    }
}
