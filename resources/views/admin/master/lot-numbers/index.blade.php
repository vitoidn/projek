@extends('layouts.dashboard')

@section('title', 'Master Lot Numbers')
@section('page-title', 'Master Lot Numbers')

@section('content')
<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="glass-card">
            <h5 class="fw-bold mb-4"><i class="ph ph-plus-circle text-primary me-2"></i> Tambah LOT</h5>
            <form action="{{ route('admin.master.lot-numbers.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small">Code (P{YY}{MM})</label>
                    <input type="text" name="code" class="form-control" placeholder="P2507" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Year</label>
                    <input type="number" name="year" class="form-control" value="{{ date('Y') }}" min="2020" max="2099" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Month (01-12)</label>
                    <input type="text" name="month" class="form-control" value="{{ date('m') }}" maxlength="2" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">Save</button>
            </form>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="glass-card">
            <h5 class="fw-bold border-bottom pb-3 mb-3"><i class="ph ph-list text-primary me-2"></i> Daftar LOT</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr><th>Code</th><th>Year</th><th>Month</th><th>Active</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @foreach($lotNumbers as $lot)
                        <tr>
                            <td class="fw-bold">{{ $lot->code }}</td>
                            <td>{{ $lot->year }}</td>
                            <td>{{ $lot->month }}</td>
                            <td>{!! $lot->is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $lot->id }}"><i class="ph ph-pencil"></i></button>
                                <form action="{{ route('admin.master.lot-numbers.destroy', $lot->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" data-confirm="Hapus lot number ini?" data-confirm-danger><i class="ph ph-trash"></i></button>
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

@foreach($lotNumbers as $lot)
<div class="modal fade" id="editModal{{ $lot->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.master.lot-numbers.update', $lot->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit LOT</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Code</label>
                        <input type="text" name="code" class="form-control" value="{{ $lot->code }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Year</label>
                        <input type="number" name="year" class="form-control" value="{{ $lot->year }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Month</label>
                        <input type="text" name="month" class="form-control" value="{{ $lot->month }}" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" value="1" id="active{{ $lot->id }}" {{ $lot->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="active{{ $lot->id }}">Active</label>
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