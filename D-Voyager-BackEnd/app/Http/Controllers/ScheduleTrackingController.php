<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleTrackingController extends Controller
{
    // DRIVER ACCEPTS A SCHEDULE
    public function accept($id)
    {
        return DB::transaction(function () use ($id) {
            $schedule = Schedule::lockForUpdate()->findOrFail($id);

            if ($schedule->driver_id !== null) {
                return response()->json(['message' => 'Schedule already taken by another driver'], 400);
            }

            $driver = \App\Models\Driver::where('user_id', auth()->id())->first();

            $schedule->update([
                'driver_id' => $driver->id
            ]);

            return response()->json([
                'message' => 'Schedule accepted successfully',
                'schedule' => $schedule
            ]);
        });
    }

    // START SCHEDULE
    public function start($id)
    {
        return DB::transaction(function () use ($id) {
            $schedule = Schedule::findOrFail($id);
            $driver = \App\Models\Driver::where('user_id', auth()->id())->first();

            if ((int) $schedule->driver_id !== (int) $driver->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            if ($schedule->start_time !== null) {
                return response()->json(['message' => 'Schedule already started'], 400);
            }

            $schedule->update([
                'start_time' => now(),
                'status' => 'on_the_way'
            ]);

            return response()->json(['message' => 'Schedule started', 'schedule' => $schedule]);
        });
    }

    // FINISH SCHEDULE
    public function finish($id)
    {
        return DB::transaction(function () use ($id) {
            $schedule = Schedule::with('bookings')->findOrFail($id);
            $driver = \App\Models\Driver::where('user_id', auth()->id())->first();

            if ((int) $schedule->driver_id !== (int) $driver->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            if ($schedule->start_time === null) {
                return response()->json(['message' => 'Schedule has not started yet'], 400);
            }

            if ($schedule->end_time !== null) {
                return response()->json(['message' => 'Schedule already finished'], 400);
            }

            $schedule->update([
                'end_time' => now(),
                'status' => 'completed'
            ]);

            // update all bookings
            foreach ($schedule->bookings as $booking) {
                $booking->update(['status' => 'completed']);
            }

            $nextSchedule = $this->ensureNextDaySchedule($schedule);

            return response()->json([
                'message' => 'Schedule finished',
                'schedule' => $schedule,
                'next_schedule' => $nextSchedule
            ]);
        });
    }

    public function track($id)
    {
        $schedule = Schedule::with('locations')->findOrFail($id);
        return response()->json($schedule);
    }

    private function ensureNextDaySchedule(Schedule $schedule): Schedule
    {
        $departureTime = Carbon::parse($schedule->departure_time);
        $nextDepartureTime = $departureTime->copy()->addDay();

        $durationMinutes = $schedule->arrival_time
            ? $departureTime->diffInMinutes(Carbon::parse($schedule->arrival_time), false)
            : null;

        $nextArrivalTime = $durationMinutes !== null
            ? $nextDepartureTime->copy()->addMinutes($durationMinutes)
            : null;

        $nextSchedule = Schedule::firstOrCreate(
            [
                'route_id' => $schedule->route_id,
                'vehicle_id' => $schedule->vehicle_id,
                'driver_id' => $schedule->driver_id,
                'departure_time' => $nextDepartureTime->format('Y-m-d H:i:s'),
            ],
            [
                'arrival_time' => $nextArrivalTime?->format('Y-m-d H:i:s'),
                'capacity' => $schedule->capacity,
                'price' => $schedule->price,
                'status' => 'scheduled',
            ]
        );

        if (!$nextSchedule->arrival_time && $nextArrivalTime) {
            $nextSchedule->arrival_time = $nextArrivalTime->format('Y-m-d H:i:s');
            $nextSchedule->save();
        }

        for ($i = 1; $i <= (int) $nextSchedule->capacity; $i++) {
            Seat::firstOrCreate(
                [
                    'schedule_id' => $nextSchedule->id,
                    'seat_number' => (string) $i,
                ],
                ['status' => 'available']
            );
        }

        return $nextSchedule;
    }
}
