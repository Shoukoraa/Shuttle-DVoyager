const fs = require('fs');

const collection = {
    info: {
        name: "Dominic Shuttle API (Updated & Perfected)",
        description: "Dokumentasi API Sistem Pemesanan Shuttle (Travel) - Final Project",
        schema: "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
    },
    variable: [
        { key: "baseUrl", value: "http://127.0.0.1:8000/api", type: "string" },
        { key: "token", value: "ISI_TOKEN_DISINI", type: "string" }
    ],
    item: [
        {
            name: "1. Auth & Profile",
            item: [
                {
                    name: "Register",
                    request: {
                        method: "POST",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Content-Type", value: "application/json" }],
                        body: { mode: "raw", raw: JSON.stringify({ name: "Budi", email: "budi@shuttle.com", password: "password123", phone: "0812345678", role: "customer" }, null, 4) },
                        url: { raw: "{{baseUrl}}/auth/register", host: ["{{baseUrl}}"], path: ["auth", "register"] }
                    }
                },
                {
                    name: "Login",
                    request: {
                        method: "POST",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Content-Type", value: "application/json" }],
                        body: { mode: "raw", raw: JSON.stringify({ email: "customer1@shuttle.com", password: "password123" }, null, 4) },
                        url: { raw: "{{baseUrl}}/auth/login", host: ["{{baseUrl}}"], path: ["auth", "login"] }
                    }
                },
                {
                    name: "Logout",
                    request: {
                        method: "POST",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        url: { raw: "{{baseUrl}}/auth/logout", host: ["{{baseUrl}}"], path: ["auth", "logout"] }
                    }
                },
                {
                    name: "Get Profile (Me)",
                    request: {
                        method: "GET",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        url: { raw: "{{baseUrl}}/me", host: ["{{baseUrl}}"], path: ["me"] }
                    }
                },
                {
                    name: "Update Profile",
                    request: {
                        method: "PUT",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Content-Type", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        body: { mode: "raw", raw: JSON.stringify({ name: "Budi Baru", phone: "0899999911" }, null, 4) },
                        url: { raw: "{{baseUrl}}/me", host: ["{{baseUrl}}"], path: ["me"] }
                    }
                }
            ]
        },
        {
            name: "2. Public (Tanpa Login)",
            item: [
                {
                    name: "Get All Schedules",
                    request: {
                        method: "GET",
                        header: [{ key: "Accept", value: "application/json" }],
                        url: { raw: "{{baseUrl}}/schedules", host: ["{{baseUrl}}"], path: ["schedules"] }
                    }
                },
                {
                    name: "Get Schedule Seats (Cek Kursi)",
                    request: {
                        method: "GET",
                        header: [{ key: "Accept", value: "application/json" }],
                        url: { raw: "{{baseUrl}}/schedules/1/seats", host: ["{{baseUrl}}"], path: ["schedules", "1", "seats"] }
                    }
                },
                {
                    name: "Track Schedule (GPS Driver)",
                    request: {
                        method: "GET",
                        header: [{ key: "Accept", value: "application/json" }],
                        url: { raw: "{{baseUrl}}/schedule/1/track", host: ["{{baseUrl}}"], path: ["schedule", "1", "track"] }
                    }
                }
            ]
        },
        {
            name: "3. Customer",
            item: [
                {
                    name: "My Bookings (Riwayat)",
                    request: {
                        method: "GET",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        url: { raw: "{{baseUrl}}/customer/my-bookings", host: ["{{baseUrl}}"], path: ["customer", "my-bookings"] }
                    }
                },
                {
                    name: "Create Booking",
                    request: {
                        method: "POST",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Content-Type", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        body: { mode: "raw", raw: JSON.stringify({ schedule_id: 1, seats: ["1", "2"] }, null, 4) },
                        url: { raw: "{{baseUrl}}/customer/booking", host: ["{{baseUrl}}"], path: ["customer", "booking"] }
                    }
                },
                {
                    name: "Cancel Booking",
                    request: {
                        method: "POST",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        url: { raw: "{{baseUrl}}/customer/booking/1/cancel", host: ["{{baseUrl}}"], path: ["customer", "booking", "1", "cancel"] }
                    }
                },
                {
                    name: "Pay Booking",
                    request: {
                        method: "POST",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        url: { raw: "{{baseUrl}}/customer/booking/1/pay", host: ["{{baseUrl}}"], path: ["customer", "booking", "1", "pay"] }
                    }
                },
                {
                    name: "Review Driver",
                    request: {
                        method: "POST",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Content-Type", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        body: { mode: "raw", raw: JSON.stringify({ rating: 5, review: "Pelayanan sangat baik dan supir ramah." }, null, 4) },
                        url: { raw: "{{baseUrl}}/customer/driver/1/review", host: ["{{baseUrl}}"], path: ["customer", "driver", "1", "review"] }
                    }
                }
            ]
        },
        {
            name: "4. Driver",
            item: [
                {
                    name: "My Schedules",
                    request: {
                        method: "GET",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        url: { raw: "{{baseUrl}}/driver/my-schedules", host: ["{{baseUrl}}"], path: ["driver", "my-schedules"] }
                    }
                },
                {
                    name: "Accept Schedule (Ambil Jadwal)",
                    request: {
                        method: "POST",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        url: { raw: "{{baseUrl}}/driver/schedule/1/accept", host: ["{{baseUrl}}"], path: ["driver", "schedule", "1", "accept"] }
                    }
                },
                {
                    name: "Start Schedule (Mulai Jalan)",
                    request: {
                        method: "POST",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        url: { raw: "{{baseUrl}}/driver/schedule/1/start", host: ["{{baseUrl}}"], path: ["driver", "schedule", "1", "start"] }
                    }
                },
                {
                    name: "Finish Schedule (Tiba di Tujuan)",
                    request: {
                        method: "POST",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        url: { raw: "{{baseUrl}}/driver/schedule/1/finish", host: ["{{baseUrl}}"], path: ["driver", "schedule", "1", "finish"] }
                    }
                },
                {
                    name: "Update Location (Kirim GPS)",
                    request: {
                        method: "POST",
                        header: [{ key: "Accept", value: "application/json" }, { key: "Content-Type", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }],
                        body: { mode: "raw", raw: JSON.stringify({ schedule_id: 1, latitude: -6.2415, longitude: 106.8433 }, null, 4) },
                        url: { raw: "{{baseUrl}}/driver/location", host: ["{{baseUrl}}"], path: ["driver", "location"] }
                    }
                }
            ]
        },
        {
            name: "5. Admin",
            item: (() => {
                const resources = [
                    { path: 'locations', name: 'Locations', body: { name: "Terminal Lebak Bulus", latitude: -6.2891, longitude: 106.7745 } },
                    { path: 'vehicles', name: 'Vehicles', body: { plate_number: "B 1234 CD", vehicle_type: "Toyota Hiace Commuter", capacity: 14, status: "active" } },
                    { path: 'routes', name: 'Routes', body: { origin_location_id: 1, destination_location_id: 2, distance_km: 155.5 } },
                    { path: 'drivers', name: 'Drivers', body: { name: "Budi Supir Baru", email: "budi.supir@shuttle.com", password: "password123", license_number: "SIM-B-0001923", status: "active" } },
                    { path: 'schedules', name: 'Schedules', body: { route_id: 1, vehicle_id: 1, driver_id: 1, departure_time: "2026-05-01 08:00:00", capacity: 14, status: "scheduled" } }
                ];
                const items = [];
                
                resources.forEach(res => {
                    items.push({
                        name: "Get All " + res.name,
                        request: { method: "GET", header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }], url: { raw: `{{baseUrl}}/admin/${res.path}`, host: ["{{baseUrl}}"], path: ["admin", res.path] } }
                    });
                    items.push({
                        name: "Create " + res.name,
                        request: { method: "POST", header: [{ key: "Accept", value: "application/json" }, { key: "Content-Type", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }], body: { mode: "raw", raw: JSON.stringify(res.body, null, 4) }, url: { raw: `{{baseUrl}}/admin/${res.path}`, host: ["{{baseUrl}}"], path: ["admin", res.path] } }
                    });
                    items.push({
                        name: "Get Single " + res.name,
                        request: { method: "GET", header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }], url: { raw: `{{baseUrl}}/admin/${res.path}/1`, host: ["{{baseUrl}}"], path: ["admin", res.path, "1"] } }
                    });
                    items.push({
                        name: "Update " + res.name,
                        request: { method: "PUT", header: [{ key: "Accept", value: "application/json" }, { key: "Content-Type", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }], body: { mode: "raw", raw: JSON.stringify(res.body, null, 4) }, url: { raw: `{{baseUrl}}/admin/${res.path}/1`, host: ["{{baseUrl}}"], path: ["admin", res.path, "1"] } }
                    });
                    items.push({
                        name: "Delete " + res.name,
                        request: { method: "DELETE", header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }], url: { raw: `{{baseUrl}}/admin/${res.path}/1`, host: ["{{baseUrl}}"], path: ["admin", res.path, "1"] } }
                    });
                });
                
                items.push({
                    name: "Get All Bookings",
                    request: { method: "GET", header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }], url: { raw: "{{baseUrl}}/admin/bookings", host: ["{{baseUrl}}"], path: ["admin", "bookings"] } }
                });

                // Tambahkan Customers (hanya read & delete)
                items.push({
                    name: "Get All Customers",
                    request: { method: "GET", header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }], url: { raw: "{{baseUrl}}/admin/customers", host: ["{{baseUrl}}"], path: ["admin", "customers"] } }
                });
                items.push({
                    name: "Get Single Customer",
                    request: { method: "GET", header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }], url: { raw: "{{baseUrl}}/admin/customers/1", host: ["{{baseUrl}}"], path: ["admin", "customers", "1"] } }
                });
                items.push({
                    name: "Delete Customer",
                    request: { method: "DELETE", header: [{ key: "Accept", value: "application/json" }, { key: "Authorization", value: "Bearer {{token}}" }], url: { raw: "{{baseUrl}}/admin/customers/1", host: ["{{baseUrl}}"], path: ["admin", "customers", "1"] } }
                });

                return items;
            })()
        }
    ]
};

fs.writeFileSync('Dominic.postman.json', JSON.stringify(collection, null, 2));
console.log('Dominic.postman.json fully perfected!');
