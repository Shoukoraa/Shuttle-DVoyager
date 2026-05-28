<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{sessionId}', function ($user, $sessionId) {
    if ($user->role && $user->role->name === 'admin') {
        return true;
    }
    $session = \App\Models\ChatSession::find($sessionId);
    return $session && (int) $session->user_id === (int) $user->id;
});

Broadcast::channel('admin.chat', function ($user) {
    return $user->role && $user->role->name === 'admin';
});

Broadcast::channel('chat.{scheduleId}.{customerId}', function ($user, $scheduleId, $customerId) {
    if ($user->role && $user->role->name === 'admin') {
        return true;
    }

    if ($user->role && $user->role->name === 'customer') {
        $customer = \App\Models\Customer::where('user_id', $user->id)->first();
        return $customer && (int) $customer->id === (int) $customerId;
    }

    if ($user->role && $user->role->name === 'driver') {
        $driver = \App\Models\Driver::where('user_id', $user->id)->first();
        if (!$driver) return false;

        $schedule = \App\Models\Schedule::find($scheduleId);
        return $schedule && (int) $schedule->driver_id === (int) $driver->id;
    }

    return false;
});

Broadcast::channel('schedules.{scheduleId}', function ($user, $scheduleId) {
    if ($user->role && $user->role->name === 'admin') {
        return true;
    }

    if ($user->role && $user->role->name === 'driver') {
        $driver = \App\Models\Driver::where('user_id', $user->id)->first();
        if (!$driver) return false;

        $schedule = \App\Models\Schedule::find($scheduleId);
        return $schedule && (int) $schedule->driver_id === (int) $driver->id;
    }

    if ($user->role && $user->role->name === 'customer') {
        $customer = \App\Models\Customer::where('user_id', $user->id)->first();
        if (!$customer) return false;

        return \App\Models\Booking::where('schedule_id', $scheduleId)
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['paid', 'booked', 'completed'])
            ->exists();
    }

    return false;
});

Broadcast::channel('admin.tracking', function ($user) {
    return $user->role && $user->role->name === 'admin';
});
