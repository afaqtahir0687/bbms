@extends('layouts.app')

@section('title', 'Allocations')

@section('content')
<div style="margin-top: 2rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h1>Allocations</h1>
        <a href="{{ route('allocations.create') }}" class="btn btn-primary">New Allocation</a>
    </div>

    <div class="glass-card" style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Billboard</th>
                    <th>Campaign</th>
                    <th>Dates</th>
                    <th>Uploader</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allocations as $allocation)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $allocation->billboard->display_name }}</div>
                        <div style="font-size:0.8rem; color:var(--text-muted);">{{ $allocation->billboard->billboard_code }}</div>
                    </td>
                    <td>
                        <div>{{ $allocation->campaign->campaign_code }}</div>
                        <div style="font-size:0.8rem; color:var(--text-muted);">{{ $allocation->campaign->customer->customer_name }}</div>
                    </td>
                    <td>
                        {{ $allocation->allocated_from->format('M d') }} - {{ $allocation->allocated_to->format('M d, Y') }}
                    </td>
                    <td>
                        @foreach($allocation->assignments as $assignment)
                            @if($assignment->active_flag)
                                {{ $assignment->uploader->full_name }}
                            @endif
                        @endforeach
                    </td>
                    <td>
                        <span class="badge {{ $allocation->allocation_status == 'Planned' ? 'badge-pending' : 'badge-active' }}">
                            {{ $allocation->allocation_status }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('allocations.show', $allocation->allocation_id) }}" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.9rem; background: rgba(255,255,255,0.1);">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 2rem; color: var(--text-muted);">No allocations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
