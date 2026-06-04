<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index(\App\Services\PaymentStatusService $paymentStatus)
    {
        $bookings = Booking::with(['customer', 'schedule.route', 'schedule.driver', 'payment'])->get();

        $bookings->each(function ($booking) use ($paymentStatus) {
            if ($booking->status === 'booked' && optional($booking->payment)->status === 'pending') {
                try {
                    $paymentStatus->syncBookingPayment($booking);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Auto-sync payment failed in Admin: ' . $e->getMessage());
                }
            }
        });

        return response()->json($bookings);
    }
}
