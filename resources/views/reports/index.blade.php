@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Laporan Absensi</h4>
    <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-success">
        <i class="bi bi-download"></i> Export ke Excel
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filter</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <div class="col-md-4">
                <label for="employee_id" class="form-label">Karyawan</label>
                <select class="form-select" id="employee_id" name="employee_id">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ $employeeId == $employee->id ? 'selected' : '' }}>
                            {{ $employee->user->name }} ({{ $employee->employee_id }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h6>Total</h6>
                <h3 class="mb-0">{{ $summary['total'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h6>Hadir</h6>
                <h3 class="mb-0">{{ $summary['present'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h6>Terlambat</h6>
                <h3 class="mb-0">{{ $summary['late'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h6>Cuti</h6>
                <h3 class="mb-0">{{ $summary['leave'] }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Absensi</h5>
        <span class="text-muted">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Karyawan</th>
                        <th>ID</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->date->format('d M Y') }}</td>
                        <td>{{ $attendance->employee->user->name }}</td>
                        <td>{{ $attendance->employee->employee_id }}</td>
                        <td>{{ $attendance->clock_in ?? '-' }}</td>
                        <td>{{ $attendance->clock_out ?? '-' }}</td>
                        <td>
                            @if($attendance->status == 'present')
                                <span class="badge bg-success">Hadir</span>
                            @elseif($attendance->status == 'late')
                                <span class="badge bg-warning">Terlambat</span>
                            @elseif($attendance->status == 'absent')
                                <span class="badge bg-danger">Tidak Hadir</span>
                            @else
                                <span class="badge bg-info">Cuti</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                            Tidak ada data absensi untuk periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
