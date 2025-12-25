@extends('layouts.app')

@section('title', $city->city_name)

@section('content')
<div style="margin-top: 2rem;">
    <div style="margin-bottom: 1rem;"><a href="{{ route('geography.showProvince', $city->province_id) }}" style="color:var(--primary); text-decoration:none;">&larr; Back to {{ $city->province->province_name }}</a></div>
    
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
         @if($city->image_path)
            <img src="{{ Storage::url($city->image_path) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
        @endif
        <h1>{{ $city->city_name }} <span style="font-size:1rem; color:var(--text-muted);">({{ $city->city_code }})</span></h1>
    </div>

    <div style="display:flex; gap: 2rem;">
        <!-- List Areas -->
        <div style="flex: 2;">
            <div class="glass-card">
                <h3 style="padding:1rem; margin:0; border-bottom:var(--border);">Areas</h3>
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
                        @forelse($city->areas as $area)
                        <tr onmouseover="highlightRow('{{ $area->area_id }}')" onmouseout="resetRow('{{ $area->area_id }}')" style="cursor: pointer; transition: background 0.2s;">
                             <td style="width: 60px;">
                                @if($area->image_path)
                                    <img src="{{ Storage::url($area->image_path) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 4px;"></div>
                                @endif
                            </td>
                            <td>{{ $area->area_code }}</td>
                            <td>{{ $area->area_name }}</td>
                            <td style="text-align: right;">
                                <a href="{{ route('geography.showArea', $area->area_id) }}" class="btn" style="padding:0.25rem 0.5rem; background:rgba(255,255,255,0.1); margin-right: 0.5rem;" title="View Billboards in Area">View Billboards</a>
                                <a href="{{ route('geography.editArea', $area->area_id) }}" class="btn" style="padding:0.25rem 0.5rem; background:rgba(59,130,246,0.2); color: #60a5fa;">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4">No areas found in {{ $city->city_name }}.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Map Display (Bottom) -->
            <div class="glass-card" style="margin-top: 2rem; padding: 0.5rem;">
                <div id="map" style="height: 400px; border-radius: 8px; z-index: 1;"></div>
                <script>
                    var mapLayers = {};
                    var originalStyles = {};
                    document.addEventListener('DOMContentLoaded', function() {
                        var streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        });
                        var satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            attribution: 'Tiles &copy; Esri'
                        });

                        var map = L.map('map', {
                            center: [{{ $city->latitude ?? 30.3753 }}, {{ $city->longitude ?? 69.3451 }}],
                            zoom: 11,
                            layers: [streetLayer]
                        });

                        L.control.layers({ "Street": streetLayer, "Satellite": satelliteLayer }).addTo(map);

                        var bounds = [];
                        @if($city->latitude && $city->longitude)
                        bounds.push([{{ $city->latitude }}, {{ $city->longitude }}]);
                        @endif

                        @foreach($city->areas as $area)
                            @if($area->latitude && $area->longitude)
                                var marker = L.marker([{{ $area->latitude }}, {{ $area->longitude }}])
                                    .addTo(map)
                                    .bindPopup('<b>{{ $area->area_name }}</b><br><a href="{{ route("geography.showArea", $area->area_id) }}">View Billboards</a>');
                                mapLayers['area_{{ $area->area_id }}'] = marker;
                                bounds.push([{{ $area->latitude }}, {{ $area->longitude }}]);
                            @endif

                            @if($area->boundary_data)
                                try {
                                    var boundary = L.geoJSON(JSON.parse('{!! $area->boundary_data !!}'), {
                                        style: { color: "#f59e0b", weight: 2, dashArray: '10, 5', fillOpacity: 0.1 }
                                    }).addTo(map);
                                    mapLayers['boundary_{{ $area->area_id }}'] = boundary;
                                    originalStyles['boundary_{{ $area->area_id }}'] = { color: "#f59e0b", weight: 2, dashArray: '10, 5', fillOpacity: 0.1 };
                                } catch(e) {}
                            @endif
                        @endforeach

                        if (bounds.length > 0) {
                            map.fitBounds(bounds, {padding: [50, 50]});
                        }
                    });

                    function highlightRow(id) {
                        var layer = mapLayers['boundary_' + id];
                        if (layer) {
                            layer.setStyle({ color: '#facc15', weight: 4, fillOpacity: 0.2 });
                            layer.bringToFront();
                        }
                        var marker = mapLayers['area_' + id];
                        if (marker) marker.openPopup();
                    }

                    function resetRow(id) {
                        var layer = mapLayers['boundary_' + id];
                        if (layer) layer.setStyle(originalStyles['boundary_' + id]);
                        var marker = mapLayers['area_' + id];
                        if (marker) marker.closePopup();
                    }
                </script>
            </div>
        </div>

        <!-- Add Area -->
        <div style="flex: 1;">
            <div class="glass-card" style="padding: 1.5rem;">
                <h3 class="mb-4">Add Area</h3>
                <form action="{{ route('geography.storeArea') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="city_id" value="{{ $city->city_id }}">
                    <div class="mb-4">
                        <input type="text" name="area_code" class="form-input" placeholder="Code (e.g. VIN)" required>
                    </div>
                    <div class="mb-4">
                        <input type="text" name="area_name" class="form-input" placeholder="Name (e.g. Vincent)" required>
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
                            var map = L.map('map-picker').setView([{{ $city->latitude ?? 30 }}, {{ $city->longitude ?? 70 }}], 10);
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
                        <label class="mb-4" style="display:block; font-size: 0.9rem; color: var(--text-muted);">Area Image</label>
                        <input type="file" name="image" class="form-input" accept="image/*">
                    </div>
                    <button class="btn btn-primary" style="width:100%;">Add Area</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
