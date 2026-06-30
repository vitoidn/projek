@extends('layouts.dashboard')

@section('title', 'Master Activity Codes')
@section('page-title', 'Master Activity Codes')

@section('content')
<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="glass-card">
            <h5 class="fw-bold mb-4"><i class="ph ph-plus-circle text-primary me-2"></i> Tambah Code</h5>
            <form action="{{ route('admin.master.activity-codes.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small">Code</label>
                    <input type="text" name="code" class="form-control" placeholder="0-9" maxlength="2" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Nama Aktivitas</label>
                    <input type="text" name="name" class="form-control" placeholder="Taisou, Running, dll" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">Save</button>
            </form>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="glass-card">
            <h5 class="fw-bold border-bottom pb-3 mb-3"><i class="ph ph-list text-primary me-2"></i> Daftar Activity Code</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr><th>Code</th><th>Aktivitas</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach($activityCodes as $ac)
                        <tr>
                            <td><span class="badge bg-secondary fs-6">{{ $ac->code }}</span></td>
                            <td class="fw-bold">{{ $ac->name }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $ac->id }}"><i class="ph ph-pencil"></i></button>
                                <form action="{{ route('admin.master.activity-codes.destroy', $ac->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" data-confirm="Hapus activity code ini?" data-confirm-danger><i class="ph ph-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@foreach($activityCodes as $ac)
<div class="modal fade" id="editModal{{ $ac->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.master.activity-codes.update', $ac->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Activity Code</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Code</label>
                        <input type="text" name="code" class="form-control" value="{{ $ac->code }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ $ac->name }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach
@endSection