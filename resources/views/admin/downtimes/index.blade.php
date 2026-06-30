@extends('layouts.dashboard')
@section('title', 'Master Data - Downtimes')
@section('page-title', 'Master Data - Downtimes')

@section('content')
<div class="glass-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Daftar Downtimes</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal"><i class="ph ph-plus"></i> Tambah Downtime</button>
    </div>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">Terjadi kesalahan pada input data.</div> @endif
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light"><tr><th>No</th><th>Nama Downtime</th><th>Tipe Kategori</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($downtimes as $key => $downtime)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td class="fw-bold">{{ $downtime->name }}</td>
                    <td><span class="badge bg-secondary">{{ $downtime->type ?? 'Lainnya' }}</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $downtime->id }}"><i class="ph ph-pencil"></i></button>
                        <form action="{{ route('admin.downtimes.destroy', $downtime->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE') <button class="btn btn-sm btn-danger" data-confirm="Yakin ingin menghapus downtime ini?" data-confirm-danger><i class="ph ph-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <div class="modal fade" id="editModal{{ $downtime->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.downtimes.update', $downtime->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header"><h5 class="modal-title fw-bold">Edit Downtime</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <div class="mb-3"><label class="form-label text-muted fw-bold">Nama Downtime</label><input type="text" name="name" class="form-control" value="{{ $downtime->name }}" required></div>
                                    <div class="mb-3"><label class="form-label text-muted fw-bold">Tipe Kategori</label><input type="text" name="type" class="form-control" value="{{ $downtime->type }}"></div>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($downtimes->isEmpty()) <tr><td colspan="4" class="text-center text-muted">Belum ada data downtime.</td></tr> @endif
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.downtimes.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title fw-bold">Tambah Downtime</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label text-muted fw-bold">Nama Downtime</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label text-muted fw-bold">Tipe Kategori</label><input type="text" name="type" class="form-control"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
