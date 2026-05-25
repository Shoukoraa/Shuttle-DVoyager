<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        return response()->json(Route::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin_name' => 'required|string',
            'destination_name' => 'required|string',
            'origin_lat' => 'required|numeric',
            'origin_lng' => 'required|numeric',
            'destination_lat' => 'required|numeric',
            'destination_lng' => 'required|numeric',
            'distance_km' => 'nullable|numeric'
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
        $route->update($request->all());
        return response()->json(['message' => 'Route updated successfully', 'data' => $route]);
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return response()->json(['message' => 'Route deleted successfully']);
    }
}
