@extends('layouts.app')

@section('title', 'Absensi Hari Ini')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Absensi Hari Ini</h4>
    <span class="text-muted">{{ now()->format('l, d F Y') }}</span>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Hadir</h5>
                <h2 class="mb-0">{{ $attendances->where('status', 'present')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Terlambat</h5>
                <h2 class="mb-0">{{ $attendances->where('status', 'late')->count() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <h5 class="card-title">Belum Absen</h5>
                <h2 class="mb-0">{{ $employees->count() - $attendances->count() }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Absensi</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Departemen</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    @php
                        $attendance = $attendances->where('employee_id', $employee->id)->first();
                    @endphp
                    <tr>
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $employee->user->name }}</td>
                        <td>{{ $employee->department ?? '-' }}</td>
                        <td>{{ $attendance?->clock_in ?? '-' }}</td>
                        <td>{{ $attendance?->clock_out ?? '-' }}</td>
                        <td>
                            @if(!$attendance)
                                <span class="badge bg-secondary">Belum Absen</span>
                            @elseif($attendance->status == 'present')
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
