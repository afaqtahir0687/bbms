@extends('layouts.app')

@section('title', 'Billboard Inventory')

@section('content')
<div style="margin-top: 2rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h1>Billboard Inventory</h1>
        <a href="{{ route('master-data.create') }}" class="btn btn-primary">Add Inventory</a>
    </div>

    <div class="glass-card" style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Display Name</th>
                    <th>Location</th>
                    <th>Type</th>
                    <th>Rating</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($billboards as $billboard)
                <tr>
                    <td style="font-weight:600;">{{ $billboard->billboard_code }}</td>
                    <td>{{ $billboard->display_name }}</td>
                    <td>
                        {{ $billboard->area->area_name }}
                        <div style="font-size:0.8rem; color:var(--text-muted);">{{ $billboard->area->city->city_name }}</div>
                    </td>
                    <td>{{ $billboard->type->type_name }}</td>
                    <td>{{ $billboard->marketRating->rating_value ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $billboard->status == 'Available' ? 'badge-active' : 'badge-pending' }}">
                            {{ $billboard->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 2rem; color: var(--text-muted);">No inventory found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
