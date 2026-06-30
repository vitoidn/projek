@extends('layouts.dashboard')
@section('title', 'Master Data - Shifts')
@section('page-title', 'Master Data - Shifts')

@section('content')
<div class="glass-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Daftar Shifts</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal"><i class="fas fa-plus"></i> Tambah Shift</button>
    </div>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">Terjadi kesalahan pada input data. Pastikan format jam HH:MM.</div> @endif
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light"><tr><th>No</th><th>Nama Shift</th><th>Start Time</th><th>End Time</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($shifts as $key => $shift)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td class="fw-bold">{{ $shift->name }}</td>
                    <td>{{ $shift->start_time ?? '-' }}</td>
                    <td>{{ $shift->end_time ?? '-' }}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $shift->id }}"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('admin.shifts.destroy', $shift->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus shift ini?');">
                            @csrf @method('DELETE') <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <div class="modal fade" id="editModal{{ $shift->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.shifts.update', $shift->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header"><h5 class="modal-title fw-bold">Edit Shift</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <div class="mb-3"><label class="form-label text-muted fw-bold">Nama Shift</label><input type="text" name="name" class="form-control" value="{{ $shift->name }}" required></div>
                                    <div class="mb-3"><label class="form-label text-muted fw-bold">Start Time</label><input type="time" name="start_time" class="form-control" value="{{ $shift->start_time }}"></div>
                                    <div class="mb-3"><label class="form-label text-muted fw-bold">End Time</label><input type="time" name="end_time" class="form-control" value="{{ $shift->end_time }}"></div>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($shifts->isEmpty()) <tr><td colspan="5" class="text-center text-muted">Belum ada data shift.</td></tr> @endif
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.shifts.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title fw-bold">Tambah Shift</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label text-muted fw-bold">Nama Shift</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label text-muted fw-bold">Start Time</label><input type="time" name="start_time" class="form-control"></div>
                    <div class="mb-3"><label class="form-label text-muted fw-bold">End Time</label><input type="time" name="end_time" class="form-control"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
