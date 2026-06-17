<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table style="border-collapse: collapse; width: 100%;">
        <thead>
            <!-- Header Laporan -->
            <tr>
                <th colspan="7" style="font-weight: bold; font-size: 16px; text-align: center; color: #1E1E1E; height: 40px; vertical-align: middle;">
                    D-VOYAGER SHUTTLE - Laporan Rekapitulasi Operasional &amp; Finansial
                </th>
            </tr>
            <tr>
                <th colspan="7" style="text-align: center; color: #6b7280; font-style: italic; height: 30px; vertical-align: middle;">
                    Periode: {{ $selected_label }} | Dicetak pada: {{ now()->timezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
                </th>
            </tr>
            
            <!-- Jarak -->
            <tr>
                <th colspan="7"></th>
            </tr>

            <!-- Summary -->
            <tr>
                <th colspan="2" style="background-color: #FBC02D; color: #1E1E1E; font-weight: bold; border: 1px solid #1E1E1E; padding: 10px; text-align: center;">
                    TOTAL BOOKING VALID
                </th>
                <th colspan="3" style="background-color: #FBC02D; color: #1E1E1E; font-weight: bold; border: 1px solid #1E1E1E; padding: 10px; text-align: center;">
                    TOTAL PENDAPATAN KOTOR
                </th>
                <th colspan="2" style="background-color: #FBC02D; color: #1E1E1E; font-weight: bold; border: 1px solid #1E1E1E; padding: 10px; text-align: center;">
                    TOTAL JADWAL
                </th>
            </tr>
            <tr>
                <th colspan="2" style="font-weight: bold; font-size: 14px; text-align: center; border: 1px solid #1E1E1E; height: 35px; vertical-align: middle;">
                    {{ number_format($monthly_booking_count, 0, ',', '.') }} Tiket
                </th>
                <th colspan="3" style="font-weight: bold; font-size: 14px; text-align: center; border: 1px solid #1E1E1E; height: 35px; vertical-align: middle;">
                    Rp {{ number_format($monthly_revenue, 0, ',', '.') }}
                </th>
                <th colspan="2" style="font-weight: bold; font-size: 14px; text-align: center; border: 1px solid #1E1E1E; height: 35px; vertical-align: middle;">
                    {{ number_format($monthly_schedules, 0, ',', '.') }} Jadwal
                </th>
            </tr>

            <!-- Jarak -->
            <tr>
                <th colspan="7"></th>
            </tr>

            <!-- Header Tabel Data -->
            <tr>
                <th style="font-weight: bold; background-color: #1E1E1E; color: #ffffff; text-align: center; border: 1px solid #000000; height: 30px; vertical-align: middle; width: 50px;">No</th>
                <th style="font-weight: bold; background-color: #1E1E1E; color: #ffffff; text-align: center; border: 1px solid #000000; vertical-align: middle; width: 120px;">Waktu (WIB)</th>
                <th style="font-weight: bold; background-color: #1E1E1E; color: #ffffff; text-align: center; border: 1px solid #000000; vertical-align: middle; width: 150px;">Pelanggan</th>
                <th style="font-weight: bold; background-color: #1E1E1E; color: #ffffff; text-align: center; border: 1px solid #000000; vertical-align: middle; width: 300px;">Rute Perjalanan</th>
                <th style="font-weight: bold; background-color: #1E1E1E; color: #ffffff; text-align: center; border: 1px solid #000000; vertical-align: middle; width: 80px;">Kursi</th>
                <th style="font-weight: bold; background-color: #1E1E1E; color: #ffffff; text-align: center; border: 1px solid #000000; vertical-align: middle; width: 120px;">Nominal (Rp)</th>
                <th style="font-weight: bold; background-color: #1E1E1E; color: #ffffff; text-align: center; border: 1px solid #000000; vertical-align: middle; width: 100px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $index => $booking)
                <tr>
                    <td style="text-align: center; border: 1px solid #000000; vertical-align: middle;">{{ $index + 1 }}</td>
                    <td style="text-align: center; border: 1px solid #000000; vertical-align: middle;">{{ \Carbon\Carbon::parse($booking->booking_time)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
                    <td style="border: 1px solid #000000; vertical-align: middle;">{{ data_get($booking, 'customer.user.name', 'Pelanggan dihapus') }}</td>
                    <td style="border: 1px solid #000000; vertical-align: middle;">{{ data_get($booking, 'schedule.route.origin.name', '-') }} - {{ data_get($booking, 'schedule.route.destination.name', '-') }}</td>
                    <td style="text-align: center; border: 1px solid #000000; vertical-align: middle;">{{ $booking->total_seat }}</td>
                    <td style="text-align: right; border: 1px solid #000000; vertical-align: middle;">{{ (int) ($booking->total_price ?? 0) }}</td>
                    <td style="text-align: center; border: 1px solid #000000; vertical-align: middle; font-weight: bold; color: {{ strtolower($booking->status) == 'paid' || strtolower($booking->status) == 'completed' ? '#059669' : '#dc2626' }};">
                        {{ strtoupper($booking->status) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
