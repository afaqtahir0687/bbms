@extends('layouts.app')

@section('title', 'Edit Country')

@section('content')
<div style="max-width: 600px; margin: 2rem auto;">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="margin: 0;">Edit Country</h1>
        <form action="{{ route('geography.destroyCountry', $country->country_id) }}" method="POST" onsubmit="return confirm('Are you sure? This will fail if the country has provinces.');">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger">Delete Country</button>
        </form>
    </div>
    
    <div class="glass-card" style="padding: 2rem;">
        <form action="{{ route('geography.updateCountry', $country->country_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="mb-4" style="display:block;">Country Code</label>
                <input type="text" name="country_code" class="form-input" value="{{ $country->country_code }}" required>
            </div>

            <div class="mb-4">
                <label class="mb-4" style="display:block;">Country Name</label>
                <input type="text" name="country_name" class="form-input" value="{{ $country->country_name }}" required>
            </div>

            <div class="mb-4">
                <label class="mb-2" style="display:block;">Location</label>
                <div style="display: flex; gap: 1rem;">
                    <div style="flex: 1;">
                        <label class="mb-2" style="display:block; font-size: 0.8rem; color: var(--text-muted);">Latitude</label>
                        <input type="text" name="latitude" class="form-input" value="{{ $country->latitude }}">
                    </div>
                    <div style="flex: 1;">
                        <label class="mb-2" style="display:block; font-size: 0.8rem; color: var(--text-muted);">Longitude</label>
                        <input type="text" name="longitude" class="form-input" value="{{ $country->longitude }}">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="mb-4" style="display:block;">Cover Image</label>
                @if($country->image_path)
                    <div style="margin-bottom: 0.5rem;">
                        <img src="{{ Storage::url($country->image_path) }}" style="width: 100px; border-radius: 8px;">
                    </div>
                @endif
                <input type="file" name="image" class="form-input" accept="image/*">
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">Upload to replace existing image.</div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Update Country</button>
                <a href="{{ route('geography.index') }}" class="btn" style="background: rgba(255,255,255,0.1); flex: 1; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
