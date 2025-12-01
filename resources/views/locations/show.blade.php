@extends('layouts.app')

@section('title', 'Detail Lokasi: ' . $location->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Detail Lokasi: {{ $location->name }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Informasi Lokasi</h5>
                    
                    <table class="table table-borderless">
                        <tr>
                            <td width="25%"><strong>Nama Lokasi</strong></td>
                            <td>{{ $location->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Alamat</strong></td>
                            <td>{{ $location->address }}</td>
                        </tr>
                        <tr>
                            <td><strong>Koordinat</strong></td>
                            <td>{{ $location->latitude }}, {{ $location->longitude }}</td>
                        </tr>
                        <tr>
                            <td><strong>Radius</strong></td>
                            <td>{{ $location->radius }} meter</td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>
                                <span class="badge {{ $location->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $location->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Deskripsi</strong></td>
                            <td>{{ $location->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat Tanggal</strong></td>
                            <td>{{ $location->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Diupdate Tanggal</strong></td>
                            <td>{{ $location->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Lokasi pada Peta</h5>
                    <div id="map" style="height: 300px;" class="border rounded"></div>
                    
                    <div class="mt-3">
                        <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}" 
                           target="_blank" 
                           class="btn btn-outline-primary btn-sm w-100">
                            <i class="mdi mdi-map-marker"></i> Buka di Google Maps
                        </a>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="card-subtitle mb-2">Aksi Lokasi</h6>
                        <div class="d-grid gap-2">
                            <a href="{{ route('locations.edit', $location->id) }}" 
                               class="btn btn-primary">
                                <i class="mdi mdi-pencil"></i> Edit Lokasi
                            </a>
                            <a href="{{ route('locations.toggle', $location->id) }}" 
                               class="btn {{ $location->is_active ? 'btn-warning' : 'btn-success' }}">
                                <i class="mdi mdi-toggle-switch"></i> 
                                {{ $location->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Daftar Karyawan -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Karyawan di Lokasi Ini</h5>
                    
                    @if($location->employees->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>NIK</th>
                                    <th>Posisi</th>
                                    <th>Departemen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($location->employees as $employee)
                                <tr>
                                    <td>{{ $employee->user->name ?? '-' }}</td>
                                    <td>{{ $employee->employee_id ?? '-' }}</td>
                                    <td>{{ $employee->position ?? '-' }}</td>
                                    <td>{{ $employee->department ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('employees.show', $employee->id) }}" 
                                           class="btn btn-outline-info btn-sm">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted">Belum ada karyawan yang ditugaskan ke lokasi ini.</p>
                    @endif
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
    // Ambil data lokasi dari model
    var latitude = {{ $location->latitude }};
    var longitude = {{ $location->longitude }};
    var radius = {{ $location->radius }};

    // Inisialisasi peta
    var map = L.map('map').setView([latitude, longitude], 17);

    // Tambahkan layer peta
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Tambahkan marker
    L.marker([latitude, longitude]).addTo(map)
        .bindPopup('{{ $location->name }}<br>{{ $location->address }}');
    
    // Tambahkan lingkaran radius
    L.circle([latitude, longitude], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.1,
        radius: radius,
        weight: 2
    }).addTo(map);
});
</script>
@endsection