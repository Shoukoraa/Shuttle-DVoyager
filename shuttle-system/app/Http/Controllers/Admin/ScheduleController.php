<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Vehicle;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index()
    {
        return response()->json(Schedule::with(['route', 'vehicle'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $vehicle = Vehicle::findOrFail($request->vehicle_id);
            $validated['capacity'] = $vehicle->capacity;
            
            $schedule = Schedule::create($validated);

            // Auto-generate seats based on vehicle capacity
            for ($i = 1; $i <= $vehicle->capacity; $i++) {
                Seat::create([
                    'schedule_id' => $schedule->id,
                    'seat_number' => (string) $i,
                    'status' => 'available'
                ]);
            }

            return response()->json([
                'message' => 'Schedule created successfully, and ' . $vehicle->capacity . ' seats initialized.',
                'data' => $schedule->load(['route', 'vehicle'])
            ], 201);
        });
    }

    public function show(Schedule $schedule)
    {
        return response()->json($schedule->load(['route', 'vehicle', 'bindings']));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'route_id' => 'sometimes|exists:routes,id',
            'vehicle_id' => 'sometimes|exists:vehicles,id',
            'driver_id' => 'sometimes|nullable|exists:drivers,id',
            'departure_time' => 'sometimes|date',
            'arrival_time' => 'sometimes|date|after:departure_time',
            'capacity' => 'sometimes|integer|min:1',
            'price' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string',
        ]);

        $schedule->update($validated);
        return response()->json(['message' => 'Schedule updated successfully', 'data' => $schedule]);
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return response()->json(['message' => 'Schedule deleted successfully']);
    }
}
