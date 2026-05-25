<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; padding: 20px; }
        .ticket-container { background-color: #fff; border-radius: 10px; max-width: 600px; margin: 0 auto; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .header { background-color: #ffc107; color: #333; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .detail-row { margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .label { font-size: 12px; color: #888; text-transform: uppercase; }
        .value { font-size: 16px; font-weight: bold; color: #333; }
        .footer { background-color: #333; color: #fff; text-align: center; padding: 15px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="header">
            <h1>E-Ticket D-Voyager Shuttle</h1>
            <p>Booking ID: #{{ $booking->id }}</p>
        </div>
        <div class="content">
            <div class="detail-row">
                <div class="label">Penumpang</div>
                <div class="value">{{ $booking->passenger_name ?? $booking->customer->user->name ?? 'Penumpang' }}</div>
            </div>
            <div class="detail-row">
                <div class="label">Rute</div>
                <div class="value">{{ $booking->schedule->route->origin->name ?? '-' }} &rarr; {{ $booking->schedule->route->destination->name ?? '-' }}</div>
            </div>
            <div class="detail-row">
                <div class="label">Jadwal Keberangkatan</div>
                <div class="value">
                    {{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('d M Y - H:i') }}
                </div>
            </div>
            <div class="detail-row">
                <div class="label">Kendaraan</div>
                <div class="value">
                    {{ $booking->schedule->vehicle->vehicle_type ?? 'Shuttle' }} ({{ $booking->schedule->vehicle->plate_number ?? '-' }})
                </div>
            </div>
            <div class="detail-row">
                <div class="label">Nomor Kursi</div>
                <div class="value">
                    {{ $booking->seats->pluck('seat_number')->join(', ') }}
                </div>
            </div>
            <div class="detail-row">
                <div class="label">Total Pembayaran</div>
                <div class="value">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</div>
            </div>
            <p style="margin-top: 20px; font-size: 14px; color: #555;">
                Terima kasih telah menggunakan layanan D-Voyager Shuttle. Silakan tunjukkan E-Ticket ini (atau manifest di aplikasi) kepada supir saat hendak naik.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} D-Voyager Shuttle. All rights reserved.
        </div>
    </div>
</body>
</html>
