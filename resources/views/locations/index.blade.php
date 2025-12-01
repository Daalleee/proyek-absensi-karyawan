@extends('layouts.app')

@section('title', 'Daftar Lokasi Kerja')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Lokasi Kerja</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <a href="{{ route('locations.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> Tambah Lokasi
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>Lokasi</th>
                                    <th>Radius</th>
                                    <th>Karyawan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($locations as $location)
                                <tr>
                                    <td>{{ $location->name }}</td>
                                    <td>{{ $location->address }}</td>
                                    <td>
                                        <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="mdi mdi-map-marker"></i> Lihat di Peta
                                        </a>
                                    </td>
                                    <td>{{ $location->radius }} meter</td>
                                    <td>{{ $location->employees_count }}</td>
                                    <td>
                                        <span class="badge {{ $location->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $location->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('locations.show', $location->id) }}" 
                                               class="btn btn-outline-info">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('locations.edit', $location->id) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form action="{{ route('locations.destroy', $location->id) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger" 
                                                        onclick="return confirm('Yakin ingin menghapus lokasi ini?')">
                                                    <i class="mdi mdi-trash-can"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('locations.toggle', $location->id) }}" 
                                               class="btn btn-outline-secondary">
                                                <i class="mdi mdi-toggle-switch"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data lokasi</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $locations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection