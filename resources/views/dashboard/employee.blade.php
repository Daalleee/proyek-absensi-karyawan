@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Selamat Datang, {{ auth()->user()->name }}</h4>
    <span class="text-muted">{{ now()->format('l, d F Y') }}</span>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-clock"></i> Absensi Hari Ini</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h1 class="display-4" id="current-time">{{ now()->format('H:i:s') }}</h1>
                    <p class="text-muted">{{ now()->format('l, d F Y') }}</p>
                </div>

                @if($employee)
                    <div class="row text-center mb-4">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <small class="text-muted">Clock In</small>
                                <h4 class="mb-0">{{ $todayAttendance?->clock_in ?? '-' }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <small class="text-muted">Clock Out</small>
                                <h4 class="mb-0">{{ $todayAttendance?->clock_out ?? '-' }}</h4>
                            </div>
                        </div>
                    </div>

                    @if($todayAttendance && $todayAttendance->status == 'late')
                        <div class="alert alert-warning text-center mb-3">
                            <i class="bi bi-exclamation-triangle"></i> Anda terlambat hari ini
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        @if(!$todayAttendance || !$todayAttendance->clock_in)
                            <form action="{{ route('attendance.clock-in') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-box-arrow-in-right"></i> Clock In
                                </button>
                            </form>
                        @elseif(!$todayAttendance->clock_out)
                            <form action="{{ route('attendance.clock-out') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-lg w-100">
                                    <i class="bi bi-box-arrow-right"></i> Clock Out
                                </button>
                            </form>
                        @else
                            <div class="alert alert-success text-center mb-0">
                                <i class="bi bi-check-circle"></i> Anda sudah absen hari ini
                            </div>
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning text-center">
                        Data karyawan tidak ditemukan. Silakan hubungi admin.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person"></i> Informasi Karyawan</h5>
            </div>
            <div class="card-body">
                @if($employee)
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">ID Karyawan</td>
                        <td><strong>{{ $employee->employee_id }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td><strong>{{ $employee->user->name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $employee->user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Departemen</td>
                        <td>{{ $employee->department ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Posisi</td>
                        <td>{{ $employee->position ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Bergabung</td>
                        <td>{{ $employee->join_date?->format('d F Y') ?? '-' }}</td>
                    </tr>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>

@if($employee && count($recentAttendances) > 0)
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Riwayat Absensi Terakhir</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAttendances as $attendance)
                    <tr>
                        <td>{{ $attendance->date->format('d M Y') }}</td>
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    setInterval(function() {
        const now = new Date();
        const time = now.toLocaleTimeString('id-ID');
        document.getElementById('current-time').textContent = time;
    }, 1000);
</script>
@endpush
