<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$schedule = \App\Models\Schedule::with('bookings')->first();
$schedule->total_passengers = $schedule->bookings->sum('total_seat');
unset($schedule->bookings);

echo json_encode($schedule);
