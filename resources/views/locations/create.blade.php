@extends('layouts.app')

@section('title', 'Tambah Lokasi Kerja')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Tambah Lokasi Kerja</h4>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('locations.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="radius" class="form-label">Radius (meter) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('radius') is-invalid @enderror" 
                                           id="radius" name="radius" value="{{ old('radius', 100) }}" min="10" max="5000" required>
                                    @error('radius')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude') }}" required>
                                    @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude') }}" required>
                                    @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                        </div>

                        <div class="mb-3">
                            <label for="map" class="form-label">Lokasi pada Peta</label>
                            <div id="map" style="height: 400px;" class="border rounded"></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('locations.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi peta
    var map = L.map('map').setView([-6.200000, 106.816666], 13); // Default ke Jakarta

    // Tambahkan layer peta
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Tandai lokasi saat ini
    var marker = L.marker([-6.200000, 106.816666]).addTo(map);
    
    // Fungsi untuk memperbarui marker saat klik
    map.on('click', function(e) {
        // Hapus marker sebelumnya
        map.removeLayer(marker);
        
        // Tambahkan marker baru
        marker = L.marker(e.latlng).addTo(map);
        
        // Update nilai latitude dan longitude
        document.getElementById('latitude').value = e.latlng.lat.toFixed(8);
        document.getElementById('longitude').value = e.latlng.lng.toFixed(8);
    });
    
    // Update marker jika ada nilai latitude/longitude dari input
    var lat = document.getElementById('latitude').value;
    var lng = document.getElementById('longitude').value;
    
    if(lat && lng) {
        map.setView([parseFloat(lat), parseFloat(lng)], 17);
        // Hapus marker default
        map.removeLayer(marker);
        // Tambahkan marker di lokasi yang ditentukan
        marker = L.marker([parseFloat(lat), parseFloat(lng)]).addTo(map);
    }
});
</script>
@endsection