@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Dashboard Admin</h4>
    <span class="text-muted">{{ now()->format('l, d F Y') }}</span>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Karyawan</h6>
                        <h2 class="mb-0 mt-2">{{ $totalEmployees }}</h2>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Hadir Hari Ini</h6>
                        <h2 class="mb-0 mt-2">{{ $presentToday }}</h2>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Terlambat</h6>
                        <h2 class="mb-0 mt-2">{{ $lateToday }}</h2>
                    </div>
                    <i class="bi bi-clock fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Absensi</h6>
                        <h2 class="mb-0 mt-2">{{ $todayAttendances }}</h2>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('employees.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-person-plus"></i> Tambah Karyawan
                    </a>
                    <a href="{{ route('attendance.today') }}" class="btn btn-outline-success">
                        <i class="bi bi-calendar-day"></i> Lihat Absensi Hari Ini
                    </a>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-info">
                        <i class="bi bi-file-earmark-bar-graph"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi</h5>
            </div>
            <div class="card-body">
                <p><strong>Jam Masuk:</strong> 08:00 WIB</p>
                <p><strong>Batas Terlambat:</strong> 08:30 WIB</p>
                <p class="mb-0"><strong>Jam Pulang:</strong> 17:00 WIB</p>
            </div>
        </div>
    </div>
</div>
@endsection
