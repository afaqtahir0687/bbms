@extends('layouts.app')

@section('title', 'Soft Bookings')

@section('content')
<div style="margin-top: 2rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h1>Soft Bookings</h1>
        <a href="{{ route('soft-bookings.create') }}" class="btn btn-primary">Create Booking</a>
    </div>

    <div class="glass-card" style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Billboard</th>
                    <th>Campaign</th>
                    <th>Hold Period</th>
                    <th>Status</th>
                    <th>Expires At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td>
                         <div style="font-weight:600;">{{ $booking->billboard->display_name }}</div>
                         <div style="font-size:0.8rem; color:var(--text-muted);">{{ $booking->billboard->billboard_code }}</div>
                    </td>
                    <td>
                        <div>{{ $booking->campaign->campaign_code }}</div>
                        <div style="font-size:0.8rem; color:var(--text-muted);">{{ $booking->campaign->customer->customer_name }}</div>
                    </td>
                    <td>
                        {{ $booking->hold_from->format('M d') }} - {{ $booking->hold_to->format('M d, Y') }}
                    </td>
                    <td>
                        <span class="badge {{ $booking->hold_status == 'HOLD' ? 'badge-pending' : 'badge-active' }}">
                            {{ $booking->hold_status }}
                        </span>
                    </td>
                    <td>
                         {{ $booking->expires_at ? $booking->expires_at->format('M d H:i') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 2rem; color: var(--text-muted);">No bookings found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
