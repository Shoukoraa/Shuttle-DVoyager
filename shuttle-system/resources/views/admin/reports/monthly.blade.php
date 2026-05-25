<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan Shuttle System</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #1f2937;
            font-size: 13px;
            line-height: 1.5;
        }
        h1, h2, h3, p {
            margin: 0 0 10px;
        }
        .muted {
            color: #6b7280;
        }
        .summary {
            display: flex;
            gap: 12px;
            margin: 18px 0 22px;
            flex-wrap: wrap;
        }
        .card {
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 14px 16px;
            min-width: 180px;
            background: #f9fafb;
        }
        .card .label {
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 6px;
        }
        .card .value {
            font-size: 18px;
            font-weight: 700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f3f4f6;
        }
        .empty {
            padding: 16px;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Laporan Bulanan Shuttle System</h1>
    <p class="muted">Periode: {{ $selected_label }}</p>
    <p class="muted">Dicetak pada: {{ now()->format('d-m-Y H:i') }}</p>

    <div class="summary">
        <div class="card">
            <div class="label">Total Booking</div>
            <div class="value">{{ number_format($monthly_booking_count, 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="label">Total Pendapatan</div>
            <div class="value">Rp {{ number_format($monthly_revenue, 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="label">Total Jadwal</div>
            <div class="value">{{ number_format($monthly_schedules, 0, ',', '.') }}</div>
        </div>
    </div>

    <h2>Detail Booking Bulanan</h2>
    @if($bookings->isEmpty())
        <div class="empty">Tidak ada booking pada periode ini.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Waktu Booking</th>
                    <th>Pelanggan</th>
                    <th>Rute</th>
                    <th>Kursi</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $index => $booking)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $booking->booking_time }}</td>
                        <td>{{ data_get($booking, 'customer.user.name', 'Pelanggan dihapus') }}</td>
                        <td>{{ data_get($booking, 'schedule.route.origin.name', '-') }} &rarr; {{ data_get($booking, 'schedule.route.destination.name', '-') }}</td>
                        <td>{{ $booking->total_seat }}</td>
                        <td>Rp {{ number_format((int) ($booking->total_price ?? 0), 0, ',', '.') }}</td>
                        <td>{{ strtoupper($booking->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
