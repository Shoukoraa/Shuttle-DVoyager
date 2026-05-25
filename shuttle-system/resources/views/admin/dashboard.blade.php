@extends('admin.layout')

@section('content')
    <style>
        .dashboard-shell {
            display: grid;
            gap: 20px;
        }

        .dashboard-top {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .dashboard-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .dashboard-actions a,
        .dashboard-actions button,
        .month-form button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 14px;
            border: 1px solid #1f2937;
            border-radius: 8px;
            text-decoration: none;
            background: #111827;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
        }

        .dashboard-actions a.secondary,
        .month-form a.secondary,
        .month-form button.secondary {
            background: #fff;
            color: #111827;
        }

        .month-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: end;
            margin-top: 8px;
        }

        .month-form label {
            display: grid;
            gap: 6px;
            font-weight: 600;
        }

        .month-form select {
            min-width: 150px;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 14px;
        }

        .stat-card,
        .panel {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fff;
            padding: 16px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
        }

        .panel h2 {
            margin: 0 0 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6b7280;
        }

        .empty-state {
            padding: 16px;
            color: #6b7280;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            text-align: center;
        }

        .archive-link {
            color: #111827;
            text-decoration: none;
            font-weight: 600;
        }
    </style>

    <div class="dashboard-shell">
        <div class="dashboard-top">
            <div>
                <h1>Dashboard Bulanan</h1>
                <p>Ringkasan laporan untuk {{ $selected_label }}.</p>
            </div>

            <div class="dashboard-actions">
                <a class="secondary" href="{{ route('admin.reports.export.excel', ['year' => $selected_year, 'month' => $selected_month]) }}">Download Excel</a>
                <a href="{{ route('admin.reports.export.pdf', ['year' => $selected_year, 'month' => $selected_month]) }}">Download PDF</a>
            </div>
        </div>

        <form class="month-form" method="GET" action="{{ route('admin.dashboard') }}">
            <label>
                Bulan
                <select name="month">
                    @foreach($available_months as $monthOption)
                        <option value="{{ $monthOption['value'] }}" @selected((int) $selected_month === (int) $monthOption['value'])>
                            {{ $monthOption['label'] }}
                        </option>
                    @endforeach
                </select>
            </label>

            <label>
                Tahun
                <select name="year">
                    @foreach($available_years as $yearOption)
                        <option value="{{ $yearOption }}" @selected((int) $selected_year === (int) $yearOption)>
                            {{ $yearOption }}
                        </option>
                    @endforeach
                </select>
            </label>

            <button type="submit">Tampilkan</button>
            <a class="secondary" href="{{ route('admin.dashboard') }}">Bulan ini</a>
        </form>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Booking Bulan Ini</div>
                <div class="stat-value">{{ number_format($monthly_booking_count, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Pendapatan Bulan Ini</div>
                <div class="stat-value">Rp {{ number_format($monthly_revenue, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Jadwal Bulan Ini</div>
                <div class="stat-value">{{ number_format($monthly_schedules, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Jadwal Hari Ini</div>
                <div class="stat-value">{{ number_format($schedules_today, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="panel">
            <h2>Booking Bulan Terpilih</h2>
            @if($bookings->isEmpty())
                <div class="empty-state">Belum ada booking pada periode ini.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Rute</th>
                            <th>Kursi</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings->take(10) as $booking)
                            <tr>
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
        </div>

        <div class="panel">
            <h2>Arsip Laporan Bulanan</h2>
            <table>
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Booking</th>
                        <th>Pendapatan</th>
                        <th>Jadwal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthly_archives as $archive)
                        <tr>
                            <td>{{ $archive['label'] }}</td>
                            <td>{{ number_format($archive['booking_count'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($archive['revenue'], 0, ',', '.') }}</td>
                            <td>{{ number_format($archive['schedule_count'], 0, ',', '.') }}</td>
                            <td>
                                <a class="archive-link" href="{{ route('admin.dashboard', ['year' => $archive['year'], 'month' => $archive['month']]) }}">Lihat</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
