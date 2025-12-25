@extends('layouts.app')

@section('title', 'Edit City')

@section('content')
<div style="max-width: 600px; margin: 2rem auto;">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="margin: 0;">Edit City</h1>
        <form action="{{ route('geography.destroyCity', $city->city_id) }}" method="POST" onsubmit="return confirm('Are you sure? This will fail if the city has areas.');">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger">Delete City</button>
        </form>
    </div>
    
    <div class="glass-card" style="padding: 2rem;">
        <form action="{{ route('geography.updateCity', $city->city_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="mb-4" style="display:block;">City Code</label>
                <input type="text" name="city_code" class="form-input" value="{{ $city->city_code }}" required>
            </div>

            <div class="mb-4">
                <label class="mb-4" style="display:block;">City Name</label>
                <input type="text" name="city_name" class="form-input" value="{{ $city->city_name }}" required>
            </div>

            <div class="mb-4">
                <label class="mb-2" style="display:block;">Location & Boundary (Draw Polygon for Boundary)</label>
                <div id="map-picker" style="height: 400px; border-radius: 8px; margin-bottom: 0.5rem; z-index: 1;"></div>
                <input type="hidden" name="boundary_data" id="boundary_data" value="{{ $city->boundary_data }}">

                <div style="display: flex; gap: 1rem;">
                    <div style="flex: 1;">
                        <label class="mb-2" style="display:block; font-size: 0.8rem; color: var(--text-muted);">Latitude</label>
                        <input type="text" id="lat" name="latitude" class="form-input" value="{{ $city->latitude }}">
                    </div>
                    <div style="flex: 1;">
                        <label class="mb-2" style="display:block; font-size: 0.8rem; color: var(--text-muted);">Longitude</label>
                        <input type="text" id="lng" name="longitude" class="form-input" value="{{ $city->longitude }}">
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var lat = {{ $city->latitude ?? 30.3753 }};
                    var lng = {{ $city->longitude ?? 69.3451 }};
                    
                    var map = L.map('map-picker').setView([lat, lng], 9);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    var drawnItems = new L.FeatureGroup();
                    map.addLayer(drawnItems);
                    
                    var marker = L.marker([lat, lng]).addTo(map);

                     // Load existing boundary
                    var existingBoundary = '{!! $city->boundary_data !!}';
                    if (existingBoundary) {
                        try {
                            var geoJsonLayer = L.geoJSON(JSON.parse(existingBoundary), {
                                style: { color: "#10b981", weight: 2 } // Green for City
                            });
                            geoJsonLayer.eachLayer(function(layer) {
                                drawnItems.addLayer(layer);
                            });
                            map.fitBounds(drawnItems.getBounds());
                        } catch (e) { console.error("Invalid GeoJSON"); }
                    }

                    var drawControl = new L.Control.Draw({
                        edit: { featureGroup: drawnItems },
                        draw: {
                            polygon: { shapeOptions: { color: '#10b981' }, allowIntersection: false },
                            marker: false, circle: false, circlemarker: false, polyline: false, rectangle: true
                        }
                    });
                    map.addControl(drawControl);

                    function updateBoundaryInput() {
                        var data = drawnItems.toGeoJSON();
                        if (data.features.length === 0) {
                            document.getElementById('boundary_data').value = '';
                        } else {
                             document.getElementById('boundary_data').value = JSON.stringify(data);
                        }
                    }

                    map.on(L.Draw.Event.CREATED, function(event) {
                        var layer = event.layer;
                        drawnItems.clearLayers();
                        drawnItems.addLayer(layer);
                        updateBoundaryInput();
                    });

                    map.on(L.Draw.Event.EDITED, updateBoundaryInput);
                    map.on(L.Draw.Event.DELETED, updateBoundaryInput);

                    map.on('click', function(e) {
                        var newLat = e.latlng.lat.toFixed(6);
                        var newLng = e.latlng.lng.toFixed(6);
                        document.getElementById('lat').value = newLat;
                        document.getElementById('lng').value = newLng;
                        marker.setLatLng(e.latlng);
                    });
                });
            </script>

            <div class="mb-4">
                <label class="mb-4" style="display:block;">Cover Image</label>
                @if($city->image_path)
                    <div style="margin-bottom: 0.5rem;">
                        <img src="{{ Storage::url($city->image_path) }}" style="width: 100px; border-radius: 8px;">
                    </div>
                @endif
                <input type="file" name="image" class="form-input" accept="image/*">
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Update City</button>
                <a href="{{ route('geography.showProvince', $city->province_id) }}" class="btn" style="background: rgba(255,255,255,0.1); flex: 1; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
