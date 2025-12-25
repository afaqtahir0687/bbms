@extends('layouts.app')

@section('title', 'Create Soft Booking')

@section('content')
<div style="max-width: 600px; margin: 2rem auto;">
    <h1 class="mb-6">Create Soft Booking (Hold)</h1>
    
    <div class="glass-card" style="padding: 2rem;">
        <form action="{{ route('soft-bookings.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="mb-4" style="display:block;">Campaign</label>
                <select name="campaign_id" class="form-select" required>
                    <option value="">Select Campaign</option>
                    @foreach($campaigns as $campaign)
                        <option value="{{ $campaign->campaign_id }}">{{ $campaign->campaign_code }} ({{ $campaign->customer->customer_name }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="mb-4" style="display:block;">Billboard</label>
                <select name="billboard_id" class="form-select" required>
                    <option value="">Select Billboard</option>
                    @foreach($billboards as $billboard)
                        <option value="{{ $billboard->billboard_id }}">{{ $billboard->display_name }} - {{ $billboard->area->area_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="mb-4">
                    <label class="mb-4" style="display:block;">Hold From</label>
                    <input type="datetime-local" name="hold_from" class="form-input" required>
                </div>
                <div class="mb-4">
                    <label class="mb-4" style="display:block;">Hold To</label>
                    <input type="datetime-local" name="hold_to" class="form-input" required>
                </div>
            </div>

             <div class="mb-4">
                <label class="mb-4" style="display:block;">Expires At (Optional)</label>
                <input type="datetime-local" name="expires_at" class="form-input">
                <div style="font-size:0.8rem; color:var(--text-muted); margin-top:0.25rem;">If set, the system can auto-release this hold.</div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Create Hold</button>
        </form>
    </div>
</div>
@endsection
