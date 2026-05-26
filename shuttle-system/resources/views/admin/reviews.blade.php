@extends('admin.layout')

@section('content')
    <h2>Ulasan Driver</h2>

    <div style="display: flex; gap: 12px; margin-bottom: 20px;">
        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
            <strong>{{ $summary['average_rating'] }}</strong>
            <div>Rata-rata Rating</div>
        </div>
        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
            <strong>{{ $summary['review_count'] }}</strong>
            <div>Total Ulasan</div>
        </div>
        <div style="padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
            <strong>{{ $summary['low_rating_count'] }}</strong>
            <div>Rating Rendah</div>
        </div>
    </div>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Booking</th>
                <th>Customer</th>
                <th>Driver</th>
                <th>Rute</th>
                <th>Rating</th>
                <th>Komentar</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reviews as $review)
                <tr>
                    <td>{{ $review->id }}</td>
                    <td>#{{ $review->booking_id ?? '-' }}</td>
                    <td>{{ $review->customer?->user?->name ?? '-' }}</td>
                    <td>{{ $review->driver?->user?->name ?? '-' }}</td>
                    <td>
                        {{ $review->booking?->schedule?->route?->origin?->name ?? '-' }}
                        &rarr;
                        {{ $review->booking?->schedule?->route?->destination?->name ?? '-' }}
                    </td>
                    <td>{{ $review->rating }}/5</td>
                    <td>{{ $review->comment ?: '-' }}</td>
                    <td>{{ optional($review->created_at)->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">Belum ada ulasan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
