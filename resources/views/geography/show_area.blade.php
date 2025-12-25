@extends('layouts.app')

@section('title', $area->area_name)

@section('content')
<div style="margin-top: 2rem;">
    <div style="margin-bottom: 1rem;"><a href="{{ route('geography.showCity', $area->city_id) }}" style="color:var(--primary); text-decoration:none;">&larr; Back to {{ $area->city->city_name }}</a></div>
    
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
         @if($area->image_path)
            <img src="{{ Storage::url($area->image_path) }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
        @endif
        <div>
            <h1>{{ $area->area_name }} <span style="font-size:1rem; color:var(--text-muted);">({{ $area->area_code }})</span></h1>
            <div style="color: var(--text-muted); margin-bottom: 0.5rem;">{{ $area->city->city_name }}, {{ $area->city->province->province_name }}, {{ $area->city->province->country->country_name }}</div>
            <a href="{{ route('geography.editArea', $area->area_id) }}" class="btn" style="padding:0.25rem 0.5rem; background:rgba(59,130,246,0.2); color: #60a5fa;">Edit Area</a>
        </div>
    </div>

    <div class="glass-card">
        <h3 style="padding:1.5rem; margin:0; border-bottom:var(--border);">Billboards in {{ $area->area_name }}</h3>
        
        <div style="padding: 1.5rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            @forelse($area->billboards as $billboard)
            <div style="background: rgba(0,0,0,0.2); border-radius: 0.5rem; border: var(--border); padding: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="font-weight: 600; font-size: 1.1rem;">{{ $billboard->display_name }}</span>
                    <span class="badge {{ $billboard->status == 'Available' ? 'badge-active' : 'badge-pending' }}">{{ $billboard->status }}</span>
                </div>
                <div style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">
                    Code: {{ $billboard->billboard_code }}
                </div>
                <div style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                    Type: {{ $billboard->type->type_name }}
                </div>
                <div style="font-size: 0.9rem;">
                     Rating: {{ $billboard->marketRating->rating_value ?? 'N/A' }}
                </div>
            </div>
            @empty
            <div style="text-align: center; color: var(--text-muted); grid-column: 1 / -1;">
                No billboards found in this area.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Map Display (Bottom) -->
    @if($area->latitude && $area->longitude)
    <div class="glass-card" style="margin-top: 2rem; padding: 0.5rem;">
        <div id="map" style="height: 300px; border-radius: 8px; z-index: 1;"></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var map = L.map('map').setView([{{ $area->latitude }}, {{ $area->longitude }}], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                L.marker([{{ $area->latitude }}, {{ $area->longitude }}]).addTo(map)
                    .bindPopup('{{ $area->area_name }}')
                    .openPopup();
                
                @if($area->boundary_data)
                    try {
                        L.geoJSON(JSON.parse('{!! $area->boundary_data !!}'), {
                            style: { color: "#f59e0b", weight: 3, fillOpacity: 0.2 }
                        }).addTo(map);
                    } catch(e) {}
                @endif
            });
        </script>
    </div>
    @endif
</div>
@endsection
