<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\AdminWebController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| ADMIN AUTH
|--------------------------------------------------------------------------
*/

// login page (tidak bisa diakses kalau sudah login)
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('login')->middleware('guest');

// proses login
Route::post('/admin/login', [AuthController::class, 'loginAdmin'])->middleware('guest');

// logout
Route::post('/admin/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/admin/login');
})->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN PROTECTED (WEB VIEWS)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    
    Route::get('/dashboard', [AdminWebController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/reports/export/excel', [AdminWebController::class, 'exportMonthlyReportExcel'])->name('admin.reports.export.excel');
    Route::get('/reports/export/pdf', [AdminWebController::class, 'exportMonthlyReportPdf'])->name('admin.reports.export.pdf');

    // LOCATIONS
    Route::get('/locations', [AdminWebController::class, 'locations'])->name('admin.locations');
    Route::post('/locations', [AdminWebController::class, 'storeLocation']);
    Route::get('/locations/{location}/edit', [AdminWebController::class, 'editLocation']);
    Route::put('/locations/{location}', [AdminWebController::class, 'updateLocation']);
    Route::delete('/locations/{location}', [AdminWebController::class, 'deleteLocation']);

    // VEHICLES
    Route::get('/vehicles', [AdminWebController::class, 'vehicles'])->name('admin.vehicles');
    Route::post('/vehicles', [AdminWebController::class, 'storeVehicle']);
    Route::get('/vehicles/{vehicle}/edit', [AdminWebController::class, 'editVehicle']);
    Route::put('/vehicles/{vehicle}', [AdminWebController::class, 'updateVehicle']);
    Route::delete('/vehicles/{vehicle}', [AdminWebController::class, 'deleteVehicle']);

    // ROUTES
    Route::get('/routes', [AdminWebController::class, 'routes'])->name('admin.routes');
    Route::post('/routes', [AdminWebController::class, 'storeRoute']);
    Route::get('/routes/{route}/edit', [AdminWebController::class, 'editRoute']);
    Route::put('/routes/{route}', [AdminWebController::class, 'updateRoute']);
    Route::delete('/routes/{route}', [AdminWebController::class, 'deleteRoute']);

    // SCHEDULES
    Route::get('/schedules', [AdminWebController::class, 'schedules'])->name('admin.schedules');
    Route::post('/schedules', [AdminWebController::class, 'storeSchedule']);
    Route::post('/schedules/bulk-delete', [AdminWebController::class, 'bulkDeleteSchedules'])->name('admin.schedules.bulk-delete');
    Route::post('/schedules/delete-all', [AdminWebController::class, 'deleteAllSchedules'])->name('admin.schedules.delete-all');
    Route::get('/schedules/{schedule}/edit', [AdminWebController::class, 'editSchedule']);
    Route::put('/schedules/{schedule}', [AdminWebController::class, 'updateSchedule']);
    Route::delete('/schedules/{schedule}', [AdminWebController::class, 'deleteSchedule']);

    // DRIVERS
    Route::get('/drivers', [AdminWebController::class, 'drivers'])->name('admin.drivers');
    Route::post('/drivers', [AdminWebController::class, 'storeDriver']);
    Route::get('/drivers/{driver}/edit', [AdminWebController::class, 'editDriver']);
    Route::put('/drivers/{driver}', [AdminWebController::class, 'updateDriver']);
    Route::delete('/drivers/{driver}', [AdminWebController::class, 'deleteDriver']);

    // CUSTOMERS
    Route::get('/customers', [AdminWebController::class, 'customers'])->name('admin.customers');
    Route::get('/customers/{customer}/edit', [AdminWebController::class, 'editCustomer']);
    Route::put('/customers/{customer}', [AdminWebController::class, 'updateCustomer']);
    Route::delete('/customers/{customer}', [AdminWebController::class, 'deleteCustomer']);

    // BOOKINGS
    Route::get('/bookings', [AdminWebController::class, 'bookings'])->name('admin.bookings');
    Route::get('/bookings/{booking}/edit', [AdminWebController::class, 'editBooking']);
    Route::put('/bookings/{booking}', [AdminWebController::class, 'updateBooking']);

    // REVIEWS
    Route::get('/reviews', [AdminWebController::class, 'reviews'])->name('admin.reviews');

    // TRACKING MAPS
    Route::get('/tracking', [AdminWebController::class, 'tracking'])->name('admin.tracking');

});

// ==========================================
// CHATBOT CUSTOMER SERVICE DEMO ROUTES
// ==========================================
Route::get('/chat-demo', function () {
    $customerRole = \App\Models\Role::where('name', 'customer')->first();
    $user = \App\Models\User::firstOrCreate(
        ['email' => 'test@example.com'],
        [
            'name' => 'Test User',
            'password' => bcrypt('password'),
            'role_id' => $customerRole ? $customerRole->id : 3,
            'phone' => '081234567890',
        ]
    );
    \Illuminate\Support\Facades\Auth::login($user);

    return view('chat_demo');
});

Route::get('/admin/active-sessions', function () {
    return \App\Models\ChatSession::with(['user', 'lastCategory'])
        ->where('status', 'active')
        ->orderBy('created_at', 'desc')
        ->get();
});

