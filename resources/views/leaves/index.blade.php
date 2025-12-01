@extends('layouts.app')

@section('title', 'Daftar Izin/Cuti')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="page-title">Izin/Cuti</h4>
                    <a href="{{ route('leaves.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus"></i> Ajukan Izin/Cuti
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Karyawan</th>
                                    <th>Jenis</th>
                                    <th>Tanggal</th>
                                    <th>Durasi</th>
                                    <th>Alasan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaves as $leave)
                                <tr>
                                    <td>
                                        {{ $leave->employee->user->name ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ $leave->employee->employee_id ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $leave->type_name }}</span>
                                    </td>
                                    <td>
                                        {{ $leave->start_date->format('d M Y') }} - 
                                        {{ $leave->end_date->format('d M Y') }}
                                    </td>
                                    <td>{{ $leave->days }} hari</td>
                                    <td>{{ Str::limit($leave->reason, 50) }}</td>
                                    <td>
                                        @if($leave->status === 'approved')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif($leave->status === 'rejected')
                                            <span class="badge bg-danger">Ditolak</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('leaves.show', $leave->id) }}" 
                                           class="btn btn-outline-info btn-sm">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        @if(Auth::user()->isAdmin() && $leave->status === 'pending')
                                            <form action="{{ route('leaves.approve', $leave->id) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" 
                                                        class="btn btn-outline-success btn-sm"
                                                        onclick="return confirm('Yakin ingin menyetujui pengajuan ini?')">
                                                    <i class="mdi mdi-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('leaves.approve', $leave->id) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" 
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Yakin ingin menolak pengajuan ini?')">
                                                    <i class="mdi mdi-close"></i>
                                                </button>
                                            </form>
                                        @elseif($leave->status === 'pending')
                                            <form action="{{ route('leaves.destroy', $leave->id) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger btn-sm"
                                                        onclick="return confirm('Yakin ingin membatalkan pengajuan ini?')">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data izin/cuti</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $leaves->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection