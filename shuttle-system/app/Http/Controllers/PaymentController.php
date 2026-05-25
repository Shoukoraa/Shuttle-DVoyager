<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketMail;

class PaymentController extends Controller
{
    public function pay(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'payment_method' => 'required|string'
        ]);

        $booking = Booking::where('id', $id)->where('customer_id', auth()->user()->customer->id ?? 0)->firstOrFail();

        if ($booking->status === 'paid') {
            return response()->json(['message' => 'Booking already paid.'], 400);
        }

        // Hapus payment yang menggantung/gagal sebelumnya jika ada
        if ($booking->payment) {
            $booking->payment()->delete();
        }

        if ((float) $request->amount !== (float) $booking->total_price) {
            return response()->json([
                'message' => 'Nominal pembayaran tidak sesuai total tagihan.',
                'expected_amount' => (float) $booking->total_price,
            ], 422);
        }

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => 'success', // In real app, this should be pending and updated via webhook
            'payment_time' => now()
        ]);

        $booking->update(['status' => 'paid']);

        try {
            $emailTarget = $booking->passenger_email ?? $booking->customer->user->email;
            Mail::to($emailTarget)->send(new TicketMail($booking));
        } catch (\Exception $e) {
            \Log::error("Failed to send ticket email: " . $e->getMessage());
        }

        return response()->json(['message' => 'Payment successful', 'payment' => $payment]);
    }
}
