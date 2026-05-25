<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerScheduleController extends Controller
{
    // API Daftar Lokasi (Kota)
    public function locations()
    {
        return response()->json(Location::all());
    }

    // API Pencarian Jadwal Berdasarkan Rute
    public function index(Request $request)
    {
        // === LOGIKA AUTO-GENERATE (TRAVELOKA STYLE) ===
        if ($request->has('origin_id') && $request->has('destination_id') && $request->has('date')) {
            $origin_id = $request->origin_id;
            $destination_id = $request->destination_id;
            $date = $request->date;

            // 1. Cari Rute yang cocok
            $route = \App\Models\Route::where('origin_location_id', $origin_id)
                      ->where('destination_location_id', $destination_id)
                      ->first();

            if ($route) {
                // 2. Ambil "Jadwal Master" dari jadwal-jadwal yang pernah dibuat untuk rute ini
                $existingSchedules = Schedule::where('route_id', $route->id)->get();
                $templates = [];
                
                foreach ($existingSchedules as $s) {
                    $time = date('H:i:s', strtotime($s->departure_time));
                    $durationMinutes = $s->arrival_time
                        ? Carbon::parse($s->departure_time)->diffInMinutes(Carbon::parse($s->arrival_time), false)
                        : null;
                    // Gunakan jam dan vehicle_id sebagai key agar bisa ada 2 mobil di jam yang sama
                    $key = $time . '_' . $s->vehicle_id;
                    if (!isset($templates[$key])) {
                        $templates[$key] = [
                            'time' => $time,
                            'duration_minutes' => $durationMinutes,
                            'vehicle_id' => $s->vehicle_id,
                            'driver_id' => $s->driver_id,
                            'capacity' => $s->capacity,
                            'price' => $s->price
                        ];
                    }
                }

                // 3. Kloning jadwal untuk tanggal yang dicari jika belum ada
                foreach ($templates as $template) {
                    $departure_datetime = $date . ' ' . $template['time'];
                    $arrival_datetime = !empty($template['duration_minutes']) && $template['duration_minutes'] > 0
                        ? Carbon::parse($departure_datetime)->addMinutes($template['duration_minutes'])
                        : null;
                    
                    $newSchedule = Schedule::firstOrCreate(
                        [
                            'route_id' => $route->id,
                            'departure_time' => $departure_datetime,
                            'vehicle_id' => $template['vehicle_id'],
                        ],
                        [
                            'driver_id' => $template['driver_id'],
                            'capacity' => $template['capacity'],
                            'price' => $template['price'],
                            'arrival_time' => $arrival_datetime,
                            'status' => 'scheduled'
                        ]
                    );

                    if (!$newSchedule->arrival_time && $arrival_datetime) {
                        $newSchedule->arrival_time = $arrival_datetime;
                        $newSchedule->save();
                    }

                    // Buat kursi secara otomatis jika jadwal ini baru saja diciptakan (Kloning)
                    if ($newSchedule->wasRecentlyCreated) {
                        for ($i = 1; $i <= $newSchedule->capacity; $i++) {
                            \App\Models\Seat::create([
                                'schedule_id' => $newSchedule->id,
                                'seat_number' => (string) $i,
                                'status' => 'available'
                            ]);
                        }
                    }
                }
            }
        }
        // ==============================================

        $query = Schedule::with(['route.origin', 'route.destination', 'vehicle']);

        if ($request->has('route_id')) {
            $query->where('route_id', $request->route_id);
        }

        // Filter berdasarkan kota asal dan tujuan
        if ($request->has('origin_id') && $request->has('destination_id')) {
            $query->whereHas('route', function($q) use ($request) {
                $q->where('origin_location_id', $request->origin_id)
                  ->where('destination_location_id', $request->destination_id);
            });
        }

        // Filter by departure date
        if ($request->has('date')) {
            $query->whereDate('departure_time', $request->date);
        }

        return response()->json($query->get());
    }

    // API Untuk Memilah Kursi yang Available pada Suatu Jadwal
    public function seats($id)
    {
        $schedule = Schedule::findOrFail($id);

        // Safety net: for schedules created from web admin, seats may not exist yet.
        $existingCount = $schedule->seats()->count();
        $capacity = (int) $schedule->capacity;

        if ($existingCount < $capacity) {
            for ($i = 1; $i <= $capacity; $i++) {
                \App\Models\Seat::firstOrCreate(
                    [
                        'schedule_id' => $schedule->id,
                        'seat_number' => (string) $i,
                    ],
                    ['status' => 'available']
                );
            }
        }
        
        $seats = $schedule->seats()->orderByRaw('CAST(seat_number AS UNSIGNED) ASC')->get();

        return response()->json([
            'schedule' => $schedule->load(['route.origin', 'route.destination', 'vehicle']),
            'seats_layout' => $seats
        ]);
    }
}
