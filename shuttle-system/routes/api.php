<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ScheduleTrackingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| PUBLIC API
|--------------------------------------------------------------------------
*/

Route::get('/test', function () {
    return response()->json(['message' => 'API OK']);
});

Route::get('/app-config', function () {
    return response()->json([
        'service_fee' => (float) config('dvoyager.service_fee', 2500),
    ]);
});

Route::get('/me', function (Request $request) {
    $token = $request->bearerToken();

    if (!$token) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $accessToken = PersonalAccessToken::findToken($token);

    if (!$accessToken || !$accessToken->tokenable) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $user = $accessToken->tokenable->loadMissing('role');
    $role = $user->role ? (string) $user->role->name : 'customer';

    $response = [
        'id' => (int) $user->id,
        'name' => (string) $user->name,
        'email' => (string) $user->email,
        'phone' => (string) ($user->phone ?? ''),
        'profile_photo_path' => (string) ($user->profile_photo_path ?? ''),
        'profile_photo_url' => $user->profile_photo_url,
        'role_id' => (int) $user->role_id,
        'role' => $role,
        'has_password' => !empty($user->password),
    ];

    // Load driver-specific data if user is a driver
    if ($role === 'driver') {
        $driver = \App\Models\Driver::where('user_id', $user->id)
            ->with('vehicle')
            ->first();

        if ($driver) {
            $averageRating = \App\Models\Review::where('driver_id', $driver->id)->avg('rating');

            $response['driver_id'] = (int) $driver->id;
            $response['license_number'] = (string) ($driver->license_number ?? '');
            $response['driver_status'] = (string) ($driver->status ?? 'inactive');
            $response['rating'] = $averageRating ? round((float) $averageRating, 1) : 0;
            $response['review_count'] = \App\Models\Review::where('driver_id', $driver->id)->count();
            $response['total_trips'] = \App\Models\Booking::whereHas('schedule', function ($query) use ($driver) {
                    $query->where('driver_id', $driver->id);
                })
                ->whereIn('status', ['completed', 'finished'])
                ->count();

            if ($driver->vehicle) {
                $response['vehicle'] = [
                    'id' => (int) $driver->vehicle->id,
                    'plate' => (string) ($driver->vehicle->plate_number ?? ''),
                    'plate_number' => (string) ($driver->vehicle->plate_number ?? ''),
                    'type' => (string) ($driver->vehicle->vehicle_type ?? ''),
                    'vehicle_type' => (string) ($driver->vehicle->vehicle_type ?? ''),
                    'vehicle_category' => (string) ($driver->vehicle->vehicle_category ?? ''),
                    'capacity' => (int) ($driver->vehicle->capacity ?? 0),
                    'status' => (string) ($driver->vehicle->status ?? 'inactive'),
                ];
            }
        }
    }

    return response()->json($response);
});

// Auth (Public)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/otp/request', [AuthController::class, 'requestOtp']);
Route::post('/auth/otp/verify', [AuthController::class, 'verifyOtp']);
Route::post('/auth/password/forgot', [AuthController::class, 'forgotPassword']);
Route::post('/auth/password/reset', [AuthController::class, 'resetPassword']);
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/auth/google', [AuthController::class, 'handleGoogleCallback']);

// Pencarian Jadwal & Lokasi (Bisa diakses tanpa login)
Route::get('/locations', [\App\Http\Controllers\CustomerScheduleController::class, 'locations']);
Route::get('/schedules', [\App\Http\Controllers\CustomerScheduleController::class, 'index']);
Route::get('/schedules/{id}/seats', [\App\Http\Controllers\CustomerScheduleController::class, 'seats']);
Route::post('/payments/dompetx/webhook', [PaymentController::class, 'webhook']);

/*
|--------------------------------------------------------------------------
| PROTECTED API (Harus Login)
|--------------------------------------------------------------------------
*/
Route::middleware('bearer.auth')->group(function () {
    Route::post('/broadcasting/auth', [\Illuminate\Broadcasting\BroadcastController::class, 'authenticate']);

    Route::get('/test-auth', function () {
        return response()->json(['message' => 'Auth OK']);
    });

    // Auth (Protected)
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Profile Management (Semua User)
    Route::put('/me', [ProfileController::class, 'update']);
    Route::post('/me/profile-photo', [ProfileController::class, 'updatePhoto']);
    Route::put('/me/password', [ProfileController::class, 'updatePassword']);

    // Notifikasi (Semua User)
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER API
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:customer')->prefix('customer')->group(function () {
        // Booking
        Route::get('/my-bookings', [BookingController::class, 'myBookings']);
        Route::post('/booking', [BookingController::class, 'store']);
        Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel']);
        
        // Payment
        Route::post('/booking/{id}/pay', [PaymentController::class, 'pay']);
        
        // Review
        Route::post('/booking/{id}/review', [ReviewController::class, 'storeForBooking']);
        Route::post('/driver/{id}/review', [ReviewController::class, 'store']);
        
        // Chat (Customer)
        Route::get('/chat/{schedule_id}', [\App\Http\Controllers\ChatController::class, 'getMessages']);
        Route::post('/chat', [\App\Http\Controllers\ChatController::class, 'sendMessage']);
    });

    /*
    |--------------------------------------------------------------------------
    | DRIVER API
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:driver')->prefix('driver')->group(function () {
        Route::get('/my-schedules', [DriverController::class, 'mySchedules']);
        Route::get('/reviews', [ReviewController::class, 'driverReviews']);
        Route::get('/schedule/{id}/manifest', [DriverController::class, 'manifest']);
        Route::post('/location', [DriverController::class, 'updateLocation']);
        
        // Menerima pesanan / Jadwal
        Route::post('/schedule/{id}/accept', [ScheduleTrackingController::class, 'accept']);

        // Schedule Control
        Route::post('/schedule/{id}/start', [ScheduleTrackingController::class, 'start']);
        Route::post('/schedule/{id}/finish', [ScheduleTrackingController::class, 'finish']);

        // Chat (Driver)
        Route::get('/chat/{schedule_id}/{customer_id}', [\App\Http\Controllers\ChatController::class, 'getMessages']);
        Route::post('/chat', [\App\Http\Controllers\ChatController::class, 'sendMessage']);
    });
    
    // Tracking trip bisa diakses oleh Customer atau siapapun yang punya ID trip (atau dibatasi jika mau)
    Route::get('/schedule/{id}/track', [ScheduleTrackingController::class, 'track']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN CONTROL PANEL API
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::apiResource('locations', \App\Http\Controllers\Admin\LocationController::class);
        Route::apiResource('routes', \App\Http\Controllers\Admin\RouteController::class);
        Route::apiResource('vehicles', \App\Http\Controllers\Admin\VehicleController::class);
        Route::apiResource('drivers', \App\Http\Controllers\Admin\DriverController::class);
        Route::apiResource('customers', \App\Http\Controllers\Admin\CustomerController::class)->only(['index', 'show', 'destroy']);
        Route::apiResource('schedules', \App\Http\Controllers\Admin\ScheduleController::class);
        Route::get('bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index']);
        Route::get('reviews', [ReviewController::class, 'adminReviews']);
    });

});
