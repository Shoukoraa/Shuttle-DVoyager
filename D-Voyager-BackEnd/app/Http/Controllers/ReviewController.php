<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    public function storeForBooking(Request $request, $booking_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $customer = auth()->user()->customer;

        if (!$customer) {
            return response()->json(['message' => 'Hanya customer yang bisa memberi rating.'], 403);
        }

        $booking = Booking::with(['schedule', 'review'])
            ->where('id', $booking_id)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $driverId = optional($booking->schedule)->driver_id;

        if (!$driverId) {
            return response()->json(['message' => 'Booking ini belum memiliki driver.'], 422);
        }

        $bookingStatus = strtolower((string) $booking->status);
        $scheduleStatus = strtolower((string) optional($booking->schedule)->status);

        if (!in_array($bookingStatus, ['completed', 'finished'], true) && $scheduleStatus !== 'completed') {
            return response()->json(['message' => 'Rating hanya bisa diberikan setelah perjalanan selesai.'], 422);
        }

        if ($booking->review) {
            throw ValidationException::withMessages([
                'booking_id' => ['Booking ini sudah diberi rating.']
            ]);
        }

        $review = Review::create([
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'driver_id' => $driverId,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json([
            'message' => 'Rating berhasil dikirim',
            'review' => $review
        ], 201);
    }

    public function driverReviews()
    {
        $driver = auth()->user()->driver;

        if (!$driver) {
            return response()->json(['message' => 'Driver tidak ditemukan.'], 403);
        }

        $reviews = Review::with([
                'customer.user',
                'booking.schedule.route.origin',
                'booking.schedule.route.destination',
            ])
            ->where('driver_id', $driver->id)
            ->latest()
            ->get();

        return response()->json([
            'average_rating' => round((float) $reviews->avg('rating'), 1),
            'review_count' => $reviews->count(),
            'reviews' => $reviews,
        ]);
    }

    public function adminReviews()
    {
        $reviews = Review::with([
                'customer.user',
                'driver.user',
                'booking.schedule.route.origin',
                'booking.schedule.route.destination',
            ])
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    public function store(Request $request, $driver_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $driver = Driver::findOrFail($driver_id);
        $customer = auth()->user()->customer;

        if (!$customer) {
            return response()->json(['message' => 'Hanya customer yang bisa memberi rating.'], 403);
        }

        $review = Review::create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json(['message' => 'Review submitted successfully', 'review' => $review]);
    }
}
