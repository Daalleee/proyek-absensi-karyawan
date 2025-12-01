@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Riwayat Absensi</h4>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        @if(auth()->user()->isAdmin())
                        <th>Karyawan</th>
                        @endif
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->date->format('d M Y') }}</td>
                        @if(auth()->user()->isAdmin())
                        <td>
                            <strong>{{ $attendance->employee->user->name }}</strong>
                            <br><small class="text-muted">{{ $attendance->employee->employee_id }}</small>
                        </td>
                        @endif
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
                        <td>{{ $attendance->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted d-block mb-2"></i>
                            Belum ada data absensi
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection
