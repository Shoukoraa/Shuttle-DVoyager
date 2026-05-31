<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan Shuttle System</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1E1E1E; /* Dark Charcoal */
            font-size: 13px;
            line-height: 1.5;
            background-color: #ffffff;
            margin: 0;
            padding: 20px;
        }
        .header-container {
            border-bottom: 2px solid #FBC02D; /* Brand Yellow */
            padding-bottom: 15px;
            margin-bottom: 25px;
            text-align: center;
        }
        .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        h1 {
            color: #1E1E1E;
            margin: 0 0 5px;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }
        h2 {
            color: #1E1E1E;
            margin: 20px 0 10px;
            font-size: 16px;
            border-left: 4px solid #FBC02D;
            padding-left: 10px;
        }
        .muted {
            color: #6b7280;
            font-size: 12px;
            margin: 0 0 5px;
        }
        .summary {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: separate;
            border-spacing: 12px 0;
        }
        .summary td {
            width: 33.33%;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .summary .label {
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .summary .value {
            font-size: 20px;
            font-weight: bold;
            color: #FBC02D;
            text-shadow: 0px 1px 1px rgba(0,0,0,0.1);
        }
        .summary .value-dark {
            color: #1E1E1E;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #e5e7eb;
            padding: 10px 12px;
            text-align: left;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #1E1E1E;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .05em;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            color: #1E1E1E;
            background-color: #FBC02D;
        }
        .empty {
            padding: 20px;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            color: #6b7280;
            text-align: center;
            font-style: italic;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header-container">
        @if(file_exists(public_path('assets/Logo_Dvoyager.png')))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/Logo_Dvoyager.png'))) }}" class="logo" alt="D-Voyager Logo">
        @endif
        <h1>D-VOYAGER SHUTTLE</h1>
        <p class="muted">Laporan Rekapitulasi Operasional & Finansial</p>
        <p class="muted" style="margin-top: 5px;">Periode: <strong>{{ $selected_label }}</strong> | Dicetak pada: {{ now()->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB</p>
    </div>

    <!-- Summary Section Using Table for PDF Compatibility -->
    <table class="summary">
        <tr>
            <td>
                <div class="label">Total Booking Valid</div>
                <div class="value value-dark">{{ number_format($monthly_booking_count, 0, ',', '.') }} <span style="font-size: 12px; color: #6b7280;">Tiket</span></div>
            </td>
            <td>
                <div class="label">Total Pendapatan Kotor</div>
                <div class="value">Rp {{ number_format($monthly_revenue, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="label">Total Perjalanan Terjadwal</div>
                <div class="value value-dark">{{ number_format($monthly_schedules, 0, ',', '.') }} <span style="font-size: 12px; color: #6b7280;">Jadwal</span></div>
            </td>
        </tr>
    </table>

    <h2>Rincian Transaksi Booking</h2>
    @if($bookings->isEmpty())
        <div class="empty">Tidak ada data transaksi booking pada periode ini.</div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Waktu (WIB)</th>
                    <th style="width: 20%;">Pelanggan</th>
                    <th style="width: 25%;">Rute Perjalanan</th>
                    <th style="width: 10%;">Kursi</th>
                    <th style="width: 15%;">Nominal</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $index => $booking)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($booking->booking_time)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
                        <td><strong>{{ data_get($booking, 'customer.user.name', 'Pelanggan dihapus') }}</strong></td>
                        <td>
                            {{ data_get($booking, 'schedule.route.origin.name', '-') }} <br>
                            <span style="color: #FBC02D;">&rarr;</span> {{ data_get($booking, 'schedule.route.destination.name', '-') }}
                        </td>
                        <td style="text-align: center;">{{ $booking->total_seat }}</td>
                        <td><strong>Rp {{ number_format((int) ($booking->total_price ?? 0), 0, ',', '.') }}</strong></td>
                        <td style="text-align: center;">
                            <span class="badge">{{ strtoupper($booking->status) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem D-Voyager pada {{ now()->timezone('Asia/Jakarta')->format('d M Y') }} &copy; PT D-Voyager Shuttle Indonesia.
    </div>
</body>
</html>
