<?php

namespace App\Http\Controllers\Web;

use App\Exports\MonthlyReportExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Location, Vehicle, Route as RouteModel, Schedule, Driver, Customer, Booking, User, Role, Review};
use App\Models\Seat;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AdminWebController extends Controller
{
    public function dashboard(Request $request) {
        return view('admin.dashboard', $this->buildMonthlyReportData($request));
    }

    public function exportMonthlyReportExcel(Request $request)
    {
        $reportData = $this->buildMonthlyReportData($request);

        return Excel::download(
            new MonthlyReportExport($reportData),
            sprintf('laporan-bulanan-%04d-%02d.xlsx', $reportData['selected_year'], $reportData['selected_month'])
        );
    }

    public function exportMonthlyReportPdf(Request $request)
    {
        $reportData = $this->buildMonthlyReportData($request);

        $pdf = Pdf::loadView('admin.reports.monthly', $reportData)
            ->setPaper('a4', 'portrait');

        return $pdf->download(sprintf('laporan-bulanan-%04d-%02d.pdf', $reportData['selected_year'], $reportData['selected_month']));
    }

    private function buildMonthlyReportData(Request $request): array
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'min:2000', 'max:' . now()->year],
            'month' => ['nullable', 'integer', 'between:1,12'],
        ]);

        $selectedYear = (int) ($validated['year'] ?? now()->year);
        $selectedMonth = (int) ($validated['month'] ?? now()->month);

        $bookingMonthlyRows = Booking::query()
            ->selectRaw('YEAR(booking_time) as report_year, MONTH(booking_time) as report_month, COUNT(*) as booking_count, COALESCE(SUM(CASE WHEN status IN ("paid", "completed") THEN total_price ELSE 0 END), 0) as revenue')
            ->groupByRaw('YEAR(booking_time), MONTH(booking_time)')
            ->get()
            ->keyBy(fn ($row) => $this->monthlyKey((int) $row->report_year, (int) $row->report_month));

        $scheduleMonthlyRows = Schedule::query()
            ->selectRaw('YEAR(departure_time) as report_year, MONTH(departure_time) as report_month, COUNT(*) as schedule_count')
            ->groupByRaw('YEAR(departure_time), MONTH(departure_time)')
            ->get()
            ->keyBy(fn ($row) => $this->monthlyKey((int) $row->report_year, (int) $row->report_month));

        $firstActivity = collect([
            Booking::min('booking_time'),
            Schedule::min('departure_time'),
        ])->filter()->min();

        $periodStart = $firstActivity ? Carbon::parse($firstActivity)->startOfMonth() : now()->startOfMonth();
        $periodEnd = now()->startOfMonth();

        if ($periodStart->greaterThan($periodEnd)) {
            $periodStart = $periodEnd->copy();
        }

        $monthlyArchives = collect(CarbonPeriod::create($periodStart, '1 month', $periodEnd))
            ->map(function (Carbon $date) use ($bookingMonthlyRows, $scheduleMonthlyRows) {
                $year = (int) $date->year;
                $month = (int) $date->month;
                $key = $this->monthlyKey($year, $month);
                $bookingRow = $bookingMonthlyRows->get($key);
                $scheduleRow = $scheduleMonthlyRows->get($key);

                return [
                    'year' => $year,
                    'month' => $month,
                    'label' => $this->monthName($month) . ' ' . $year,
                    'booking_count' => (int) ($bookingRow->booking_count ?? 0),
                    'revenue' => (int) ($bookingRow->revenue ?? 0),
                    'schedule_count' => (int) ($scheduleRow->schedule_count ?? 0),
                ];
            })
            ->values();

        $selectedArchive = $monthlyArchives->first(function (array $row) use ($selectedYear, $selectedMonth) {
            return $row['year'] === $selectedYear && $row['month'] === $selectedMonth;
        });

        if (! $selectedArchive) {
            $selectedArchive = [
                'year' => $selectedYear,
                'month' => $selectedMonth,
                'label' => $this->monthName($selectedMonth) . ' ' . $selectedYear,
                'booking_count' => 0,
                'revenue' => 0,
                'schedule_count' => 0,
            ];
        }

        $bookings = Booking::with(['customer.user', 'schedule.route.origin', 'schedule.route.destination'])
            ->whereYear('booking_time', $selectedYear)
            ->whereMonth('booking_time', $selectedMonth)
            ->orderByDesc('booking_time')
            ->get();

        $availableYears = $monthlyArchives
            ->pluck('year')
            ->push($selectedYear)
            ->unique()
            ->sortDesc()
            ->values();

        return [
            'selected_year' => $selectedYear,
            'selected_month' => $selectedMonth,
            'selected_label' => $this->monthName($selectedMonth) . ' ' . $selectedYear,
            'monthly_booking_count' => (int) ($selectedArchive['booking_count'] ?? 0),
            'monthly_revenue' => (int) ($selectedArchive['revenue'] ?? 0),
            'monthly_schedules' => (int) ($selectedArchive['schedule_count'] ?? 0),
            'schedules_today' => Schedule::whereDate('departure_time', today())->count(),
            'bookings' => $bookings,
            'monthly_archives' => $monthlyArchives,
            'available_years' => $availableYears,
            'available_months' => collect(range(1, 12))->map(fn (int $month) => [
                'value' => $month,
                'label' => $this->monthName($month),
            ])->all(),
        ];
    }

    private function monthlyKey(int $year, int $month): string
    {
        return sprintf('%04d-%02d', $year, $month);
    }

    private function monthName(int $month): string
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ][$month] ?? 'Bulan Tidak Dikenal';
    }

    // LOCATIONS
    public function locations() {
        $locations = Location::orderByDesc('id')->paginate(15)->withQueryString();
        return view('admin.locations', compact('locations'));
    }
    public function storeLocation(Request $request) {
        $validated = $request->validate(['name' => 'required|string|max:255', 'latitude' => 'required|numeric', 'longitude' => 'required|numeric']);
        Location::create($validated);
        return back()->with('success', 'Lokasi berhasil ditambahkan!');
    }
    public function editLocation(Location $location) { return view('admin.locations_edit', compact('location')); }
    public function updateLocation(Request $request, Location $location) {
        $validated = $request->validate(['name' => 'required|string|max:255', 'latitude' => 'required|numeric', 'longitude' => 'required|numeric']);
        $location->update($validated);
        return redirect()->route('admin.locations')->with('success', 'Lokasi berhasil diperbarui!');
    }
    public function deleteLocation(Location $location) {
        try { $location->delete(); return back()->with('success', 'Lokasi berhasil dihapus!'); }
        catch (\Exception $e) { return back()->with('error', 'Gagal menghapus lokasi karena sedang digunakan oleh rute.'); }
    }

    public function bulkDeleteLocations(Request $request) {
        $validated = $request->validate([ 'location_ids' => 'required|array|min:1', 'location_ids.*' => 'integer|exists:locations,id' ]);
        try { $count = Location::whereIn('id', $validated['location_ids'])->delete(); return back()->with('success', $count . ' lokasi berhasil dihapus.'); } 
        catch (\Exception $e) { return back()->with('error', 'Gagal menghapus lokasi terpilih.'); }
    }

    public function deleteAllLocations() {
        try { $count = Location::query()->delete(); return back()->with('success', $count . ' lokasi berhasil dihapus semua.'); } 
        catch (\Exception $e) { return back()->with('error', 'Gagal menghapus semua lokasi.'); }
    }

    // VEHICLES
    public function vehicles() {
        $vehicles = Vehicle::orderByDesc('id')->paginate(15)->withQueryString();
        return view('admin.vehicles', compact('vehicles'));
    }
    public function storeVehicle(Request $request) {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20',
            'vehicle_type' => 'required|string|max:50',
            'vehicle_category' => 'required|in:family_car,mini_bus,bus',
            'capacity' => 'required|integer|min:1'
        ]);

        $existingVehicle = Vehicle::withTrashed()
            ->where('plate_number', $validated['plate_number'])
            ->first();

        if ($existingVehicle) {
            if ($existingVehicle->trashed()) {
                $existingVehicle->restore();
                $existingVehicle->update([
                    'vehicle_type' => $validated['vehicle_type'],
                    'vehicle_category' => $validated['vehicle_category'],
                    'capacity' => $validated['capacity'],
                    'status' => 'active',
                ]);

                return back()->with('success', 'Kendaraan lama dengan plat yang sama berhasil dipulihkan!');
            }

            return back()
                ->withErrors(['plate_number' => 'Plat nomor sudah digunakan oleh kendaraan aktif.'])
                ->withInput();
        }

        Vehicle::create($validated);
        return back()->with('success', 'Kendaraan berhasil ditambahkan!');
    }
    public function editVehicle(Vehicle $vehicle) { return view('admin.vehicles_edit', compact('vehicle')); }
    public function updateVehicle(Request $request, Vehicle $vehicle) {
        $validated = $request->validate([
            'plate_number' => ['required', 'string', 'max:20', Rule::unique('vehicles', 'plate_number')->ignore($vehicle->id)],
            'vehicle_type' => 'required|string|max:50',
            'vehicle_category' => 'required|in:family_car,mini_bus,bus',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string'
        ]);
        $vehicle->update($validated);
        return redirect()->route('admin.vehicles')->with('success', 'Kendaraan berhasil diperbarui!');
    }
    public function deleteVehicle(Vehicle $vehicle) {
        try { $vehicle->delete(); return back()->with('success', 'Kendaraan berhasil dihapus!'); }
        catch (\Exception $e) { return back()->with('error', 'Gagal menghapus kendaraan karena sedang digunakan di jadwal.'); }
    }

    public function bulkDeleteVehicles(Request $request) {
        $validated = $request->validate([ 'vehicle_ids' => 'required|array|min:1', 'vehicle_ids.*' => 'integer|exists:vehicles,id' ]);
        try { $count = Vehicle::whereIn('id', $validated['vehicle_ids'])->delete(); return back()->with('success', $count . ' kendaraan berhasil dihapus.'); } 
        catch (\Exception $e) { return back()->with('error', 'Gagal menghapus kendaraan terpilih.'); }
    }

    public function deleteAllVehicles() {
        try { $count = Vehicle::query()->delete(); return back()->with('success', $count . ' kendaraan berhasil dihapus semua.'); } 
        catch (\Exception $e) { return back()->with('error', 'Gagal menghapus semua kendaraan.'); }
    }

    // ROUTES
    public function routes() {
        $routes = RouteModel::with(['origin', 'destination'])
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
        $locations = Location::all();
        return view('admin.routes', compact('routes', 'locations'));
    }
    public function storeRoute(Request $request) {
        $validated = $request->validate(['origin_location_id' => 'required|exists:locations,id', 'destination_location_id' => 'required|exists:locations,id|different:origin_location_id', 'distance_km' => 'required|numeric|min:0.1', 'price' => 'required|integer|min:0']);
        RouteModel::create($validated);
        return back()->with('success', 'Rute berhasil ditambahkan!');
    }
    public function editRoute(RouteModel $route) { return view('admin.routes_edit', ['route' => $route, 'locations' => Location::all()]); }
    public function updateRoute(Request $request, RouteModel $route) {
        $validated = $request->validate(['origin_location_id' => 'required|exists:locations,id', 'destination_location_id' => 'required|exists:locations,id|different:origin_location_id', 'distance_km' => 'required|numeric|min:0.1', 'price' => 'required|integer|min:0']);
        $route->update($validated);
        return redirect()->route('admin.routes')->with('success', 'Rute berhasil diperbarui!');
    }
    public function deleteRoute(RouteModel $route) {
        try { $route->delete(); return back()->with('success', 'Rute berhasil dihapus!'); }
        catch (\Exception $e) { return back()->with('error', 'Gagal menghapus rute karena ada jadwal terkait.'); }
    }

    public function bulkDeleteRoutes(Request $request) {
        $validated = $request->validate([ 'route_ids' => 'required|array|min:1', 'route_ids.*' => 'integer|exists:routes,id' ]);
        try { $count = RouteModel::whereIn('id', $validated['route_ids'])->delete(); return back()->with('success', $count . ' rute berhasil dihapus.'); } 
        catch (\Exception $e) { return back()->with('error', 'Gagal menghapus rute terpilih.'); }
    }

    public function deleteAllRoutes() {
        try { $count = RouteModel::query()->delete(); return back()->with('success', $count . ' rute berhasil dihapus semua.'); } 
        catch (\Exception $e) { return back()->with('error', 'Gagal menghapus semua rute.'); }
    }

    // SCHEDULES
    public function schedules() {
        $schedules = Schedule::with(['route.origin', 'route.destination', 'vehicle', 'driver.user'])
            ->orderByDesc('departure_time')
            ->paginate(15)
            ->withQueryString();
        $routes = RouteModel::with(['origin', 'destination'])->get();
        $vehicles = Vehicle::all();
        $drivers = Driver::with('user')->get();
        return view('admin.schedules', compact('schedules', 'routes', 'vehicles', 'drivers'));
    }
    public function storeSchedule(Request $request) {
        $validated = $request->validate(['route_id' => 'required|exists:routes,id', 'vehicle_id' => 'required|exists:vehicles,id', 'driver_id' => 'required|exists:drivers,id', 'departure_time' => 'required|date', 'arrival_time' => 'required|date|after:departure_time', 'capacity' => 'required|integer|min:1', 'price' => 'required|numeric|min:0']);
        $validated['status'] = 'scheduled';

        DB::transaction(function () use ($validated) {
            $schedule = Schedule::create($validated);

            for ($i = 1; $i <= (int) $schedule->capacity; $i++) {
                Seat::firstOrCreate(
                    [
                        'schedule_id' => $schedule->id,
                        'seat_number' => (string) $i,
                    ],
                    ['status' => 'available']
                );
            }
        });

        return back()->with('success', 'Jadwal berhasil ditambahkan!');
    }
    public function editSchedule(Schedule $schedule) { return view('admin.schedules_edit', ['schedule' => $schedule, 'routes' => RouteModel::with(['origin', 'destination'])->get(), 'vehicles' => Vehicle::all(), 'drivers' => Driver::with('user')->get()]); }
    public function updateSchedule(Request $request, Schedule $schedule) {
        $validated = $request->validate(['route_id' => 'required|exists:routes,id', 'vehicle_id' => 'required|exists:vehicles,id', 'driver_id' => 'required|exists:drivers,id', 'departure_time' => 'required|date', 'arrival_time' => 'required|date|after:departure_time', 'capacity' => 'required|integer|min:1', 'price' => 'required|numeric|min:0', 'status' => 'required|string']);
        $schedule->update($validated);
        return redirect()->route('admin.schedules')->with('success', 'Jadwal berhasil diperbarui!');
    }
    public function deleteSchedule(Schedule $schedule) {
        try { $schedule->delete(); return back()->with('success', 'Jadwal berhasil dihapus!'); }
        catch (\Exception $e) { return back()->with('error', 'Gagal menghapus jadwal.'); }
    }

    public function bulkDeleteSchedules(Request $request) {
        $validated = $request->validate([
            'schedule_ids' => 'required|array|min:1',
            'schedule_ids.*' => 'integer|exists:schedules,id',
        ]);

        try {
            $count = Schedule::whereIn('id', $validated['schedule_ids'])->delete();
            return back()->with('success', $count . ' jadwal berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus jadwal terpilih.');
        }
    }

    public function deleteAllSchedules() {
        try {
            $count = Schedule::query()->delete();
            return back()->with('success', $count . ' jadwal berhasil dihapus semua.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus semua jadwal.');
        }
    }

    // DRIVERS
    public function drivers() {
        $drivers = Driver::with(['user', 'vehicle'])
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
        $vehicles = Vehicle::all();
        return view('admin.drivers', compact('drivers', 'vehicles'));
    }
    public function storeDriver(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string',
            'license_number' => 'required|string',
            'vehicle_id' => 'nullable|integer|exists:vehicles,id',
            'profile_photo' => 'required|image|max:2048'
        ]);

        $driverRoleId = Role::where('name', 'driver')->value('id');
        if (!$driverRoleId) {
            return back()->with('error', 'Role driver tidak ditemukan.');
        }

        $existingUser = User::withTrashed()->where('email', $validated['email'])->first();

        if ($existingUser) {
            if (!$existingUser->trashed()) {
                return back()->withErrors(['email' => 'Email sudah digunakan akun aktif.'])->withInput();
            }

            if ((int) $existingUser->role_id !== (int) $driverRoleId) {
                return back()->withErrors(['email' => 'Email ini milik role lain dan tidak bisa dipakai untuk driver.'])->withInput();
            }

            $profilePhotoPath = $existingUser->profile_photo_path;
            if ($request->hasFile('profile_photo')) {
                if ($profilePhotoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($profilePhotoPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($profilePhotoPath);
                }
                $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
            }

            $existingUser->restore();
            $existingUser->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
                'role_id' => $driverRoleId,
                'profile_photo_path' => $profilePhotoPath,
            ]);

            $driver = Driver::withTrashed()->where('user_id', $existingUser->id)->first();

            if ($driver) {
                if ($driver->trashed()) {
                    $driver->restore();
                }

                $driver->update([
                    'license_number' => $validated['license_number'],
                    'status' => 'active',
                ]);
            } else {
                Driver::create([
                    'user_id' => $existingUser->id,
                    'license_number' => $validated['license_number'],
                    'status' => 'active'
                ]);
            }

            // Assign vehicle jika ada
            if ($validated['vehicle_id'] ?? null) {
                Vehicle::where('driver_id', '!=', null)->where('id', $validated['vehicle_id'])->update(['driver_id' => null]);
                Vehicle::find($validated['vehicle_id'])->update(['driver_id' => $driver->id]);
            }

            return back()->with('success', 'Akun driver lama berhasil dipulihkan!');
        }

        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role_id' => $driverRoleId,
            'profile_photo_path' => $profilePhotoPath
        ]);
        
        $driver = Driver::create(['user_id' => $user->id, 'license_number' => $validated['license_number'], 'status' => 'active']);
        
        // Assign vehicle jika ada
        if ($validated['vehicle_id'] ?? null) {
            Vehicle::where('driver_id', '!=', null)->where('id', $validated['vehicle_id'])->update(['driver_id' => null]);
            Vehicle::find($validated['vehicle_id'])->update(['driver_id' => $driver->id]);
        }
        
        return back()->with('success', 'Supir berhasil ditambahkan!');
    }
    public function editDriver(Driver $driver) { 
        $vehicles = Vehicle::all();
        return view('admin.drivers_edit', compact('driver', 'vehicles')); 
    }
    public function updateDriver(Request $request, Driver $driver) {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($driver->user_id)],
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string',
            'license_number' => 'required|string',
            'status' => 'required|string',
            'vehicle_id' => 'nullable|integer|exists:vehicles,id',
            'profile_photo' => 'nullable|image|max:2048'
        ]);
        $profilePhotoPath = $driver->user->profile_photo_path;
        if ($request->hasFile('profile_photo')) {
            if ($profilePhotoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($profilePhotoPath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($profilePhotoPath);
            }
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        $driver->user->update([
            'name' => $validated['name'], 
            'email' => $validated['email'], 
            'phone' => $validated['phone'] ?? null,
            'profile_photo_path' => $profilePhotoPath
        ]);
        if (!empty($validated['password'])) { $driver->user->update(['password' => Hash::make($validated['password'])]); }
        $driver->update(['license_number' => $validated['license_number'], 'status' => $validated['status']]);
        
        // Handle vehicle assignment
        if ($validated['vehicle_id'] ?? null) {
            // Clear vehicle dari driver lain jika vehicle dipilih
            Vehicle::where('driver_id', '!=', null)->where('id', $validated['vehicle_id'])->update(['driver_id' => null]);
            Vehicle::find($validated['vehicle_id'])->update(['driver_id' => $driver->id]);
        } elseif ($driver->vehicle) {
            // Jika vehicle_id null, unassign vehicle dari driver ini
            $driver->vehicle->update(['driver_id' => null]);
        }
        
        return redirect()->route('admin.drivers')->with('success', 'Data supir berhasil diperbarui!');
    }
    public function deleteDriver(Driver $driver) {
        try { 
            $user = $driver->user; 
            \App\Models\Vehicle::where('driver_id', $driver->id)->update(['driver_id' => null]);
            $driver->delete(); 
            if($user) $user->delete(); 
            return back()->with('success', 'Supir berhasil dihapus!'); 
        }
        catch (\Exception $e) { return back()->with('error', 'Gagal menghapus supir.'); }
    }

    public function bulkDeleteDrivers(Request $request) {
        $validated = $request->validate([ 'driver_ids' => 'required|array|min:1', 'driver_ids.*' => 'integer|exists:drivers,id' ]);
        try { 
            $count = 0;
            $drivers = Driver::whereIn('id', $validated['driver_ids'])->get();
            foreach ($drivers as $d) { 
                $user = $d->user; 
                \App\Models\Vehicle::where('driver_id', $d->id)->update(['driver_id' => null]);
                $d->delete(); 
                if($user) $user->delete(); 
                $count++; 
            }
            return back()->with('success', $count . ' supir berhasil dihapus.'); 
        } catch (\Exception $e) { return back()->with('error', 'Gagal menghapus supir terpilih.'); }
    }

    public function deleteAllDrivers() {
        try { 
            $count = 0;
            foreach (Driver::all() as $d) { 
                $user = $d->user; 
                \App\Models\Vehicle::where('driver_id', $d->id)->update(['driver_id' => null]);
                $d->delete(); 
                if($user) $user->delete(); 
                $count++; 
            }
            return back()->with('success', $count . ' supir berhasil dihapus semua.'); 
        } catch (\Exception $e) { return back()->with('error', 'Gagal menghapus semua supir.'); }
    }

    // CUSTOMERS
    public function customers() {
        // Backfill safety: ensure every user with customer role has a customer profile row
        $customerRoleId = Role::where('name', 'customer')->value('id');

        if ($customerRoleId) {
            $existingUserIds = Customer::pluck('user_id');

            User::where('role_id', $customerRoleId)
                ->whereNotIn('id', $existingUserIds)
                ->select('id')
                ->get()
                ->each(function ($user) {
                    Customer::create(['user_id' => $user->id]);
                });
        }

        $customers = Customer::with('user')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers', compact('customers'));
    }
    public function editCustomer(Customer $customer) { return view('admin.customers_edit', compact('customer')); }
    public function updateCustomer(Request $request, Customer $customer) {
        $request->validate(['name' => 'required|string', 'email' => 'required|email|unique:users,email,'.$customer->user_id, 'password' => 'nullable|min:6', 'phone' => 'nullable|string']);
        $customer->user->update(['name' => $request->name, 'email' => $request->email, 'phone' => $request->phone]);
        if ($request->password) { $customer->user->update(['password' => Hash::make($request->password)]); }
        return redirect()->route('admin.customers')->with('success', 'Data pelanggan berhasil diperbarui!');
    }
    public function deleteCustomer(Customer $customer) {
        try { $user = $customer->user; $customer->delete(); if($user) $user->delete(); return back()->with('success', 'Pelanggan berhasil dihapus!'); }
        catch (\Exception $e) { return back()->with('error', 'Gagal menghapus pelanggan.'); }
    }

    public function bulkDeleteCustomers(Request $request) {
        $validated = $request->validate([ 'customer_ids' => 'required|array|min:1', 'customer_ids.*' => 'integer|exists:customers,id' ]);
        try { 
            $count = 0;
            $customers = Customer::whereIn('id', $validated['customer_ids'])->get();
            foreach ($customers as $c) { $user = $c->user; $c->delete(); if($user) $user->delete(); $count++; }
            return back()->with('success', $count . ' pelanggan berhasil dihapus.'); 
        } catch (\Exception $e) { return back()->with('error', 'Gagal menghapus pelanggan terpilih.'); }
    }

    public function deleteAllCustomers() {
        try { 
            $count = 0;
            foreach (Customer::all() as $c) { $user = $c->user; $c->delete(); if($user) $user->delete(); $count++; }
            return back()->with('success', $count . ' pelanggan berhasil dihapus semua.'); 
        } catch (\Exception $e) { return back()->with('error', 'Gagal menghapus semua pelanggan.'); }
    }

    // BOOKINGS
    public function bookings() {
        $bookings = Booking::with(['customer.user', 'schedule.route'])
            ->orderByDesc('booking_time')
            ->paginate(15)
            ->withQueryString();
        return view('admin.bookings', compact('bookings'));
    }
    public function editBooking(Booking $booking) { return view('admin.bookings_edit', compact('booking')); }
    public function updateBooking(Request $request, Booking $booking) {
        $validated = $request->validate(['status' => 'required|string']);
        $oldStatus = $booking->status;
        $booking->update(['status' => $validated['status']]);

        // Auto release seats if cancelled
        if ($oldStatus !== 'cancelled' && $validated['status'] === 'cancelled') {
            foreach ($booking->seats as $seat) {
                $seat->update(['status' => 'available']);
            }
        }

        return redirect()->route('admin.bookings')->with('success', 'Status transaksi berhasil diperbarui!');
    }

    // REVIEWS
    public function reviews() {
        $reviews = Review::with([
            'customer.user',
            'driver.user',
            'booking.schedule.route.origin',
            'booking.schedule.route.destination',
        ])->latest()->paginate(15)->withQueryString();

        $summary = [
            'average_rating' => round((float) Review::avg('rating'), 1),
            'review_count' => $reviews->total(),
            'low_rating_count' => Review::where('rating', '<=', 2)->count(),
        ];

        return view('admin.reviews', compact('reviews', 'summary'));
    }

    // TRACKING MAPS
    public function tracking() { 
        // Mengambil jadwal yang sedang berjalan beserta lokasinya
        $activeSchedules = Schedule::where('status', 'on_the_way')
            ->with(['vehicle', 'driver.user', 'route.origin', 'route.destination', 'locations' => function($query) {
                $query->latest('recorded_at'); // Mengurutkan lokasi terbaru
            }])
            ->get();
            
        return view('admin.tracking', ['activeSchedules' => $activeSchedules]); 
    }

    // CHAT CS
    public function chat() {
        return view('admin.chat');
    }
}
