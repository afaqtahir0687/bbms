@extends('layouts.app')

@section('title', $country->country_name)

@section('content')
<div style="margin-top: 2rem;">
    <div style="margin-bottom: 1rem;"><a href="{{ route('geography.index') }}" style="color:var(--primary); text-decoration:none;">&larr; Back to Countries</a></div>
    
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
         @if($country->image_path)
            <img src="{{ Storage::url($country->image_path) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
        @endif
        <h1>{{ $country->country_name }} <span style="font-size:1rem; color:var(--text-muted);">({{ $country->country_code }})</span></h1>
    </div>

    <div style="display:flex; gap: 2rem;">
        <!-- List Provinces -->
        <div style="flex: 2;">
            <div class="glass-card">
                <h3 style="padding:1rem; margin:0; border-bottom:var(--border);">Provinces</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($country->provinces as $province)
                        <tr onmouseover="highlightRow('{{ $province->province_id }}')" onmouseout="resetRow('{{ $province->province_id }}')" style="cursor: pointer; transition: background 0.2s;">
                             <td style="width: 60px;">
                                @if($province->image_path)
                                    <img src="{{ Storage::url($province->image_path) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 4px;"></div>
                                @endif
                            </td>
                            <td>{{ $province->province_code }}</td>
                            <td>{{ $province->province_name }}</td>
                            <td style="text-align: right;">
                                <a href="{{ route('geography.showProvince', $province->province_id) }}" class="btn" style="padding:0.25rem 0.5rem; background:rgba(255,255,255,0.1); margin-right: 0.5rem;">Manage</a>
                                <a href="{{ route('geography.editProvince', $province->province_id) }}" class="btn" style="padding:0.25rem 0.5rem; background:rgba(59,130,246,0.2); color: #60a5fa;">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4">No provinces found in {{ $country->country_name }}.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Map Display (Bottom) -->
            <div class="glass-card" style="margin-top: 2rem; padding: 0.5rem;">
                <div id="map" style="height: 600px; border-radius: 8px; z-index: 1;"></div>
                <script>
                    var mapLayers = {};
                    var originalStyles = {};
                    
                    // Colors matching typical Pakistan political maps
                    var provinceColors = {
                        'Balochistan': '#F4A460',      // Sandy Brown / Orange
                        'Punjab': '#32CD32',           // Lime Green
                        'Sindh': '#1E90FF',            // Dodger Blue
                        'Khyber Pakhtunkhwa': '#FFD700', // Gold / Yellow
                        'Gilgit-Baltistan': '#8A2BE2', // Blue Violet
                        'Islamabad': '#FF4500',        // Orange Red
                        'Azad Kashmir': '#FF69B4'      // Hot Pink / Light Red
                    };

                    document.addEventListener('DOMContentLoaded', function() {
                        // Use CartoDB Positron for a clean, label-free background to let our map shine
                        var cleanLayer = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                            subdomains: 'abcd',
                            maxZoom: 20
                        });

                        var map = L.map('map', {
                            center: [{{ $country->latitude ?? 30.3753 }}, {{ $country->longitude ?? 69.3451 }}],
                            zoom: 6,
                            layers: [cleanLayer],
                            zoomControl: false // Cleaner look, add back if needed
                        });
                        
                        L.control.zoom({ position: 'topright' }).addTo(map);

                        var bounds = [];
                        
                        // Custom Icon for Labels
                         function createLabelIcon(text) {
                            return L.divIcon({
                                className: 'map-label-icon',
                                html: '<div style="background: rgba(255,255,255,0.7); padding: 2px 8px; border-radius: 4px; border: 1px solid #666; font-weight: bold; font-size: 10px; color: black; white-space: nowrap; transform: translate(-50%, -50%); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">' + text + '</div>',
                                iconSize: null // Auto size
                            });
                        }

                        @foreach($country->provinces as $prov)
                            @if($prov->boundary_data)
                                try {
                                    var provName = "{{ $prov->province_name }}";
                                    var color = provinceColors[provName] || '#999999';
                                    
                                    var boundary = L.geoJSON(JSON.parse('{!! $prov->boundary_data !!}'), {
                                        style: { 
                                            color: 'white',       // White border for political map look
                                            weight: 1, 
                                            opacity: 1,
                                            fillColor: color, 
                                            fillOpacity: 1      // Solid fill
                                        }
                                    }).addTo(map);
                                    
                                    mapLayers['boundary_{{ $prov->province_id }}'] = boundary;
                                    originalStyles['boundary_{{ $prov->province_id }}'] = { color: 'white', weight: 1, opacity: 1, fillColor: color, fillOpacity: 1 };
                                    
                                    // Calculate center for label
                                    var center = boundary.getBounds().getCenter();
                                    
                                    // Add Label
                                    L.marker(center, {
                                        icon: createLabelIcon(provName),
                                        interactive: false
                                    }).addTo(map);

                                    // Add invisible marker for clicking/popup if needed, or bind to polygon
                                    boundary.bindPopup('<b>{{ $prov->province_name }}</b><br><a href="{{ route("geography.showProvince", $prov->province_id) }}">Manage</a>');
                                    
                                    // Add to bounds
                                    var geoJsonData = JSON.parse('{!! $prov->boundary_data !!}');
                                    // Simple way to extend bounds with geojson layer bounds
                                    map.fitBounds(boundary.getBounds()); // This will be overwritten by final fitBounds
                                    
                                } catch(e) { console.error("Error drawing " + provName, e); }
                            @endif
                            
                            // Keep track of coordinates for fitBounds
                            @if($prov->latitude && $prov->longitude)
                                bounds.push([{{ $prov->latitude }}, {{ $prov->longitude }}]);
                            @endif
                        @endforeach

                        if (bounds.length > 0) {
                            map.fitBounds(bounds, {padding: [50, 50]});
                        }
                    });

                    function highlightRow(id) {
                        var layer = mapLayers['boundary_' + id];
                        if (layer) {
                             layer.setStyle({ weight: 3, color: '#333', fillOpacity: 0.8 }); // Highlight effect
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

        <!-- Add Province -->
        <div style="flex: 1;">
            <div class="glass-card" style="padding: 1.5rem;">
                <h3 class="mb-4">Add Province</h3>
                <form action="{{ route('geography.storeProvince') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="country_id" value="{{ $country->country_id }}">
                    <div class="mb-4">
                        <input type="text" name="province_code" class="form-input" placeholder="Code (e.g. EC)" required>
                    </div>
                    <div class="mb-4">
                        <input type="text" name="province_name" class="form-input" placeholder="Name (e.g. Eastern Cape)" required>
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
                            // Center on Country if available, else Pakistan default
                            var defaultLat = {{ $country->latitude ?? 30.3753 }};
                            var defaultLng = {{ $country->longitude ?? 69.3451 }};
                            
                            var map = L.map('map-picker').setView([defaultLat, defaultLng], 6); 
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
                        <label class="mb-4" style="display:block; font-size: 0.9rem; color: var(--text-muted);">Province Image</label>
                        <input type="file" name="image" class="form-input" accept="image/*">
                    </div>
                    <button class="btn btn-primary" style="width:100%;">Add Province</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
