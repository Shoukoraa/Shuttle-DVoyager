<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index()
    {
        return response()->json(Booking::with(['customer', 'driver', 'schedule.route'])->get());
    }
}
