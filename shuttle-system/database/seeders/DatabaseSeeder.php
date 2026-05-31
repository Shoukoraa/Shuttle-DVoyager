<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Driver;
use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\Location;
use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Setup Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $driverRole = Role::firstOrCreate(['name' => 'driver']);
        $customerRole = Role::firstOrCreate(['name' => 'customer']);

        // 2. Setup Admin
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@shuttle.com')],
            [
                'name' => 'Super Admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password123')),
                'phone' => '085289692234',
                'role_id' => $adminRole->id
            ]
        );

        // 3. Sample driver and customer
        $driverUser = User::firstOrCreate(
            ['email' => 'driver1@shuttle.com'],
            [
                'name' => 'Driver One',
                'password' => Hash::make('driverpass'),
                'phone' => '0812222222',
                'role_id' => $driverRole->id
            ]
        );

        $customerUser = User::firstOrCreate(
            ['email' => 'customer1@shuttle.com'],
            [
                'name' => 'Customer One',
                'password' => Hash::make('customerpass'),
                'phone' => '0813333333',
                'role_id' => $customerRole->id
            ]
        );

        // 4. Create driver/customer records
        $driver = Driver::firstOrCreate(
            ['user_id' => $driverUser->id],
            ['license_number' => 'SIM123456', 'status' => 'active']
        );
        $customer = Customer::firstOrCreate(['user_id' => $customerUser->id]);

        // 5. Create locations (for route)
        $originLocation = Location::firstOrCreate(
            ['name' => 'Kampus Pusat'],
            ['latitude' => -6.200000, 'longitude' => 106.816666]
        );
        $destinationLocation = Location::firstOrCreate(
            ['name' => 'Terminal Pusat'],
            ['latitude' => -6.250000, 'longitude' => 106.850000]
        );

        // 6. Create route with location IDs
        $route = Route::firstOrCreate([
            'origin_location_id' => $originLocation->id,
            'destination_location_id' => $destinationLocation->id,
        ], [
            'distance_km' => 15.5
        ]);

        // 7. Sample vehicle with driver_id
        $vehicle = Vehicle::firstOrCreate(
            ['plate_number' => 'B1234CD'],
            [
                'vehicle_type' => 'minibus',
                'vehicle_category' => 'mini_bus',
                'capacity' => 12,
                'driver_id' => $driver->id,
                'status' => 'active'
            ]
        );

        // 8. Sample schedule
        Schedule::firstOrCreate([
            'route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'departure_time' => Carbon::now()->addHours(1),
        ], [
            'arrival_time' => Carbon::now()->addHours(3),
            'capacity' => 12,
            'status' => 'scheduled'
        ]);

        $this->call([
            ChatbotSeeder::class,
        ]);
    }
}
