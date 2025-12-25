@extends('layouts.app')

@section('title', 'New Allocation')

@section('content')
<div style="max-width: 600px; margin: 2rem auto;">
    <h1 class="mb-6">Crate New Allocation</h1>
    
    <div class="glass-card" style="padding: 2rem;">
        <form action="{{ route('allocations.store') }}" method="POST">
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
                    <label class="mb-4" style="display:block;">From Date</label>
                    <input type="date" name="allocated_from" class="form-input" required>
                </div>
                <div class="mb-4">
                    <label class="mb-4" style="display:block;">To Date</label>
                    <input type="date" name="allocated_to" class="form-input" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Create Allocation</button>
        </form>
    </div>
</div>
@endsection
