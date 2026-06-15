<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        return response()->json(Route::with(['origin', 'destination'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin_location_id' => 'required|exists:locations,id',
            'destination_location_id' => 'required|exists:locations,id|different:origin_location_id',
            'distance_km' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0'
        ]);

        $route = Route::create($validated);
        return response()->json(['message' => 'Route created successfully', 'data' => $route], 201);
    }

    public function show(Route $route)
    {
        return response()->json($route);
    }

    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'origin_location_id' => 'sometimes|exists:locations,id',
            'destination_location_id' => 'sometimes|exists:locations,id|different:origin_location_id',
            'distance_km' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0'
        ]);

        $route->update($validated);
        return response()->json(['message' => 'Route updated successfully', 'data' => $route->load(['origin', 'destination'])]);
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return response()->json(['message' => 'Route deleted successfully']);
    }
}
