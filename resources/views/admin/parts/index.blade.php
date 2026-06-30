@extends('layouts.dashboard')

@section('title', 'Master Data - Parts')
@section('page-title', 'Master Data - Parts')

@section('sidebar')
    <li>
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
    </li>
    <li class="active">
        <a href="{{ route('admin.parts.index') }}"><i class="fas fa-database"></i> Master Data</a>
    </li>
    <li>
        <a href="#"><i class="fas fa-users"></i> Users & Roles</a>
    </li>
    <li>
        <a href="#"><i class="fas fa-history"></i> Audit Logs</a>
    </li>
@endsection

@section('content')
<div class="glass-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Daftar Parts</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="fas fa-plus"></i> Tambah Part
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">Terjadi kesalahan pada input data.</div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Part Code</th>
                    <th>Lot Number</th>
                    <th>Qty / Lot</th>
                    <th>Cycle Time (s)</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($parts as $key => $part)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td class="fw-bold">{{ $part->part_code }}</td>
                    <td>{{ $part->lot_number ?? '-' }}</td>
                    <td>{{ $part->qty_per_lot }} pcs</td>
                    <td>{{ $part->cycle_time_sec }} s</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $part->id }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.parts.destroy', $part->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus part ini?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal{{ $part->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.parts.update', $part->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Edit Part</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label text-muted fw-bold">Part Code</label>
                                        <input type="text" name="part_code" class="form-control" value="{{ $part->part_code }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted fw-bold">Lot Number</label>
                                        <input type="text" name="lot_number" class="form-control" value="{{ $part->lot_number }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted fw-bold">Qty / Lot (pcs)</label>
                                        <input type="number" name="qty_per_lot" class="form-control" value="{{ $part->qty_per_lot }}" required min="1">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted fw-bold">Cycle Time (detik)</label>
                                        <input type="number" name="cycle_time_sec" class="form-control" value="{{ $part->cycle_time_sec }}" required min="1">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($parts->isEmpty())
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada data part.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.parts.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Part Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Part Code</label>
                        <input type="text" name="part_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Lot Number</label>
                        <input type="text" name="lot_number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Qty / Lot (pcs)</label>
                        <input type="number" name="qty_per_lot" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Cycle Time (detik)</label>
                        <input type="number" name="cycle_time_sec" class="form-control" required min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
