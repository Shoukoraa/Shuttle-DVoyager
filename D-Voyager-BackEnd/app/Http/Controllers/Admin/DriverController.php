<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function index()
    {
        // Load user data as well
        return response()->json(Driver::with('user')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'license_number' => 'required|string',
            'status' => 'in:active,inactive'
        ]);

        return DB::transaction(function () use ($validated) {
            // 1. Buat User baru dengan role driver
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => \App\Models\Role::where('name', 'driver')->first()->id
            ]);

            // 2. Buat profil Driver
            $driver = Driver::create([
                'user_id' => $user->id,
                'license_number' => $validated['license_number'],
                'status' => $validated['status'] ?? 'active'
            ]);

            return response()->json(['message' => 'Driver created successfully', 'data' => $driver->load('user')], 201);
        });
    }

    public function show(Driver $driver)
    {
        return response()->json($driver->load('user'));
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $driver->user_id,
            'license_number' => 'sometimes|string',
            'status' => 'in:active,inactive'
        ]);

        return DB::transaction(function () use ($request, $driver) {
            // Update data User jika ada
            if ($request->has('name') || $request->has('email')) {
                $driver->user->update($request->only(['name', 'email']));
            }
            
            // Update data Driver
            $driver->update($request->only(['license_number', 'status']));
            
            return response()->json(['message' => 'Driver updated successfully', 'data' => $driver->load('user')]);
        });
    }

    public function destroy(Driver $driver)
    {
        return DB::transaction(function () use ($driver) {
            $user = $driver->user;
            
            // Hapus profil driver
            $driver->delete();
            
            // Hapus juga akun usernya secara permanen
            if ($user) {
                $user->delete();
            }
            
            return response()->json(['message' => 'Driver and associated user deleted successfully']);
        });
    }
}
