<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $driver_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $driver = Driver::findOrFail($driver_id);

        $review = Review::create([
            'customer_id' => auth()->user()->customer->id ?? 0,
            'driver_id' => $driver->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json(['message' => 'Review submitted successfully', 'review' => $review]);
    }
}
