@extends('layouts.app')

@section('title', 'Add Inventory')

@section('content')
<div style="max-width: 600px; margin: 2rem auto;">
    <h1 class="mb-6">Add New Billboard</h1>
    
    <div class="glass-card" style="padding: 2rem;">
        <form action="{{ route('master-data.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="mb-4" style="display:block;">Billboard Code</label>
                <input type="text" name="billboard_code" class="form-input" required placeholder="e.g. JHB-001">
            </div>

            <div class="mb-4">
                <label class="mb-4" style="display:block;">Display Name</label>
                <input type="text" name="display_name" class="form-input" required placeholder="e.g. Highway Digital North">
            </div>

            <div class="mb-4">
                <label class="mb-4" style="display:block;">Area</label>
                <select name="area_id" class="form-select" required>
                    <option value="">Select Area</option>
                    @foreach($areas as $area)
                    <option value="{{ $area->area_id }}">{{ $area->area_name }} ({{ $area->city->city_name }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="mb-4" style="display:block;">Type</label>
                <select name="billboard_type_id" class="form-select" required>
                    <option value="">Select Type</option>
                    @foreach($types as $type)
                    <option value="{{ $type->billboard_type_id }}">{{ $type->type_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="mb-4" style="display:block;">Market Rating</label>
                <select name="market_rating_id" class="form-select">
                    <option value="">None</option>
                    @foreach($ratings as $rating)
                    <option value="{{ $rating->rating_id }}">{{ $rating->rating_value }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="mb-4" style="display:block;">Status</label>
                <select name="status" class="form-select" required>
                    <option value="Available">Available</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Decommissioned">Decommissioned</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Add Inventory</button>
        </form>
    </div>
</div>
@endsection
