@extends('layouts.app')

@section('title', 'Detail Izin/Cuti')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Detail Izin/Cuti</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title">Informasi Pengajuan</h5>
                        <span class="badge 
                            @if($leave->status === 'approved') bg-success
                            @elseif($leave->status === 'rejected') bg-danger
                            @else bg-warning
                            @endif">
                            {{ $leave->status === 'approved' ? 'Disetujui' : ($leave->status === 'rejected' ? 'Ditolak' : 'Pending') }}
                        </span>
                    </div>
                    
                    <table class="table table-borderless">
                        <tr>
                            <td width="25%"><strong>Karyawan</strong></td>
                            <td>{{ $leave->employee->user->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>ID Karyawan</strong></td>
                            <td>{{ $leave->employee->employee_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jenis Izin/Cuti</strong></td>
                            <td>{{ $leave->type_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Pengajuan</strong></td>
                            <td>{{ $leave->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Mulai</strong></td>
                            <td>{{ $leave->start_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Selesai</strong></td>
                            <td>{{ $leave->end_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Durasi</strong></td>
                            <td>{{ $leave->days }} hari</td>
                        </tr>
                        <tr>
                            <td><strong>Alasan</strong></td>
                            <td>{{ $leave->reason }}</td>
                        </tr>
                        @if($leave->attachment)
                        <tr>
                            <td><strong>Lampiran</strong></td>
                            <td>
                                <a href="{{ asset('storage/' . $leave->attachment) }}" 
                                   target="_blank" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="mdi mdi-file"></i> Lihat Lampiran
                                </a>
                            </td>
                        </tr>
                        @endif
                        @if($leave->status !== 'pending')
                        <tr>
                            <td><strong>Disetujui Oleh</strong></td>
                            <td>{{ $leave->approver->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Disetujui</strong></td>
                            <td>{{ $leave->approved_at ? $leave->approved_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                        @endif
                        @if($leave->admin_notes)
                        <tr>
                            <td><strong>Catatan Admin</strong></td>
                            <td>{{ $leave->admin_notes }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        
        @if(Auth::user()->isAdmin() && $leave->status === 'pending')
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Aksi Admin</h5>
                    <form action="{{ route('leaves.approve', $leave->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="approved">Setujui</option>
                                <option value="rejected">Tolak</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" name="admin_notes" rows="3"></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-check"></i> Setujui
                            </button>
                            <button type="submit" class="btn btn-danger" 
                                    formaction="{{ route('leaves.approve', ['leave' => $leave->id, 'status' => 'rejected']) }}">
                                <i class="mdi mdi-close"></i> Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="row mt-3">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <a href="{{ route('leaves.index') }}" class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
@endsection