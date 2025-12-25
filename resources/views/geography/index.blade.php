@extends('layouts.app')

@section('title', 'Geography Management')

@section('content')
<div style="margin-top: 2rem;">
    <h1>Geography Management</h1>
    <p style="color:var(--text-muted); margin-bottom: 2rem;">Manage Countries, Provinces, Cities, and Areas.</p>

    <div style="display:flex; gap: 2rem;">
        
        <!-- List Countries -->
        <div style="flex: 2;">
            <div class="glass-card">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Country Code</th>
                            <th>Name</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($countries as $country)
                        <tr onmouseover="highlightRow('{{ $country->country_id }}')" onmouseout="resetRow('{{ $country->country_id }}')" style="cursor: pointer; transition: background 0.2s;">
                            <td style="width: 60px;">
                                @if($country->image_path)
                                    <img src="{{ Storage::url($country->image_path) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 4px;"></div>
                                @endif
                            </td>
                            <td>{{ $country->country_code }}</td>
                            <td>{{ $country->country_name }}</td>
                            <td style="text-align: right;">
                                <a href="{{ route('geography.showCountry', $country->country_id) }}" class="btn" style="padding:0.25rem 0.5rem; background:rgba(255,255,255,0.1); margin-right: 0.5rem;">Manage</a>
                                <a href="{{ route('geography.editCountry', $country->country_id) }}" class="btn" style="padding:0.25rem 0.5rem; background:rgba(59,130,246,0.2); color: #60a5fa;">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4">No countries found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Global Map (Bottom) -->
            <div class="glass-card" style="margin-top: 2rem; padding: 0.5rem;">
                <div id="map-global" style="height: 500px; border-radius: 8px; z-index: 1;"></div>
                <script>
                    var mapLayers = {}; 
                    var originalStyles = {};

                    document.addEventListener('DOMContentLoaded', function() {
                         var cleanLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
                            attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
                            subdomains: 'abcd',
                            maxZoom: 20
                        });

                        var map = L.map('map-global', {
                            center: [30, 70],
                            zoom: 4,
                            layers: [cleanLayer],
                            zoomControl: false
                        });
                        
                        L.control.zoom({ position: 'topright' }).addTo(map);

                        var bounds = [];
                        @foreach($countries as $country)
                            // For Global Map, we might just want to show the country boundary
                            // But usually we show provinces if it's a Pakistan app. 
                            // Since we only have Pakistan provinces seeded, we can reuse the logic if we iterate provinces
                            // But here we are iterating countries. 
                            // Let's assume the country boundary is the outline.
                            // If we want the COLORFUL map of Pakistan, we need to draw its provinces here too?
                            // The user said "pakistan maps". 
                            // If this is the country list, showing just the outline of Pakistan is standard.
                            // But if we want it to look like the image, we should probably fetch provinces for the map.
                            // However, let's just style the COUNTRY boundary nicely for now.
                            
                             @if($country->boundary_data)
                                try {
                                    var boundary = L.geoJSON(JSON.parse('{!! $country->boundary_data !!}'), {
                                        style: { color: "#555", weight: 2, fillColor: "#eeeeee", fillOpacity: 0.5 }
                                    }).addTo(map);
                                    
                                    mapLayers['boundary_{{ $country->country_id }}'] = boundary;
                                    originalStyles['boundary_{{ $country->country_id }}'] = { color: "#555", weight: 2, fillColor: "#eeeeee", fillOpacity: 0.5 };
                                    
                                    // Add Label
                                    var center = boundary.getBounds().getCenter();
                                     L.marker(center, {
                                        icon: L.divIcon({
                                            className: 'country-label',
                                            html: '<div style="font-weight:bold; font-size:14px; text-shadow:0 0 3px white;">{{ $country->country_name }}</div>',
                                            iconSize: null
                                        }),
                                        interactive: false
                                    }).addTo(map);
                                    
                                } catch(e) {}
                            @endif
                            
                            @if($country->latitude && $country->longitude)
                                bounds.push([{{ $country->latitude }}, {{ $country->longitude }}]);
                            @endif
                        @endforeach

                        if (bounds.length > 0) {
                            map.fitBounds(bounds, {padding: [50, 50]});
                        }
                    });

                    function highlightRow(id) {
                        var layer = mapLayers['boundary_' + id];
                        if (layer) {
                            layer.setStyle({ color: '#facc15', weight: 4, fillOpacity: 0.1 }); 
                            layer.bringToFront();
                        }
                    }

                    function resetRow(id) {
                        var layer = mapLayers['boundary_' + id];
                        if (layer) layer.setStyle(originalStyles['boundary_' + id]);
                    }
                </script>
            </div>
        </div>

        <!-- Add Country -->
        <div style="flex: 1;">
            <div class="glass-card" style="padding: 1.5rem;">
                <h3 class="mb-4">Add Country</h3>
                <form action="{{ route('geography.storeCountry') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <input type="text" name="country_code" class="form-input" placeholder="Code (e.g. ZA)" required>
                    </div>
                    <div class="mb-4">
                        <input type="text" name="country_name" class="form-input" placeholder="Name (e.g. South Africa)" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="mb-2" style="display:block;">Location (Click on map to set)</label>
                        <div id="map-picker" style="height: 200px; border-radius: 8px; margin-bottom: 0.5rem; z-index: 1;"></div>
                        <div style="display: flex; gap: 1rem;">
                            <input type="text" id="lat" name="latitude" class="form-input" placeholder="Latitude" readonly>
                            <input type="text" id="lng" name="longitude" class="form-input" placeholder="Longitude" readonly>
                        </div>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var map = L.map('map-picker').setView([30.3753, 69.3451], 5); // Default: Pakistan
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap contributors'
                            }).addTo(map);

                            var marker;

                            map.on('click', function(e) {
                                var lat = e.latlng.lat.toFixed(6);
                                var lng = e.latlng.lng.toFixed(6);

                                document.getElementById('lat').value = lat;
                                document.getElementById('lng').value = lng;

                                if (marker) {
                                    marker.setLatLng(e.latlng);
                                } else {
                                    marker = L.marker(e.latlng).addTo(map);
                                }
                            });
                        });
                    </script>

                     <div class="mb-4">
                        <label class="mb-4" style="display:block; font-size: 0.9rem; color: var(--text-muted);">Cover Image</label>
                        <input type="file" name="image" class="form-input" accept="image/*">
                    </div>
                    <button class="btn btn-primary" style="width:100%;">Add Country</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
