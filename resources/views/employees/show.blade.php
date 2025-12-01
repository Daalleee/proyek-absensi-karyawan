@extends('layouts.app')

@section('title', 'Detail Karyawan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Detail Karyawan</h4>
    <div>
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>
                </div>
                <h5>{{ $employee->user->name }}</h5>
                <p class="text-muted mb-1">{{ $employee->employee_id }}</p>
                <p class="text-muted">{{ $employee->position ?? 'Tidak ada posisi' }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Informasi Karyawan</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted" width="30%">ID Karyawan</td>
                        <td>{{ $employee->employee_id }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama Lengkap</td>
                        <td>{{ $employee->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $employee->user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. Telepon</td>
                        <td>{{ $employee->phone ?? '-' }}</td>
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
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Absensi Terakhir</h5>
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
                            @forelse($employee->attendances->take(10) as $attendance)
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
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">
                                    Belum ada data absensi
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
