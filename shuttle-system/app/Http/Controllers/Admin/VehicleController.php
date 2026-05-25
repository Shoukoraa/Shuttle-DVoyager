<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        return response()->json(Vehicle::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles',
            'vehicle_type' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'status' => 'in:active,inactive'
        ]);

        $vehicle = Vehicle::create($validated);
        return response()->json(['message' => 'Vehicle created successfully', 'data' => $vehicle], 201);
    }

    public function show(Vehicle $vehicle)
    {
        return response()->json($vehicle);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $vehicle->update($request->all());
        return response()->json(['message' => 'Vehicle updated successfully', 'data' => $vehicle]);
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return response()->json(['message' => 'Vehicle deleted successfully']);
    }
}
