@extends('layouts.app')

@section('title', $province->province_name)

@section('content')
<div style="margin-top: 2rem;">
    <div style="margin-bottom: 1rem;"><a href="{{ route('geography.showCountry', $province->country_id) }}" style="color:var(--primary); text-decoration:none;">&larr; Back to {{ $province->country->country_name }}</a></div>
    
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
         @if($province->image_path)
            <img src="{{ Storage::url($province->image_path) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
        @endif
        <h1>{{ $province->province_name }} <span style="font-size:1rem; color:var(--text-muted);">({{ $province->province_code }})</span></h1>
    </div>

    <div style="display:flex; gap: 2rem;">
        <!-- List Cities -->
        <div style="flex: 2;">
            <div class="glass-card">
                <h3 style="padding:1rem; margin:0; border-bottom:var(--border);">Cities</h3>
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
                        @forelse($province->cities as $city)
                        <tr>
                            <td style="width: 60px;">
                                @if($city->image_path)
                                    <img src="{{ Storage::url($city->image_path) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 4px;"></div>
                                @endif
                            </td>
                            <td>{{ $city->city_code }}</td>
                            <td>{{ $city->city_name }}</td>
                            <td style="text-align: right;">
                                <a href="{{ route('geography.showCity', $city->city_id) }}" class="btn" style="padding:0.25rem 0.5rem; background:rgba(255,255,255,0.1); margin-right: 0.5rem;">Manage</a>
                                <a href="{{ route('geography.editCity', $city->city_id) }}" class="btn" style="padding:0.25rem 0.5rem; background:rgba(59,130,246,0.2); color: #60a5fa;">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4">No cities found in {{ $province->province_name }}.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add City -->
        <div style="flex: 1;">
            <div class="glass-card" style="padding: 1.5rem;">
                <h3 class="mb-4">Add City</h3>
                <form action="{{ route('geography.storeCity') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="province_id" value="{{ $province->province_id }}">
                    <div class="mb-4">
                        <input type="text" name="city_code" class="form-input" placeholder="Code (e.g. EL)" required>
                    </div>
                    <div class="mb-4">
                        <input type="text" name="city_name" class="form-input" placeholder="Name (e.g. East London)" required>
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
                            var map = L.map('map-picker').setView([{{ $province->latitude ?? 30 }}, {{ $province->longitude ?? 70 }}], 6);
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
                        <label class="mb-4" style="display:block; font-size: 0.9rem; color: var(--text-muted);">City Image</label>
                        <input type="file" name="image" class="form-input" accept="image/*">
                    </div>
                    <button class="btn btn-primary" style="width:100%;">Add City</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
