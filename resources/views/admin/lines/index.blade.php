@extends('layouts.dashboard')
@section('title', 'Master Data - Lines')
@section('page-title', 'Master Data - Lines')

@section('content')
<div class="glass-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Daftar Lines</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal"><i class="ph ph-plus"></i> Tambah Line</button>
    </div>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">Terjadi kesalahan pada input data.</div> @endif
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light"><tr><th>No</th><th>Nama Line</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @foreach($lines as $key => $line)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td class="fw-bold">{{ $line->name }}</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $line->id }}"><i class="ph ph-pencil"></i></button>
                        <form action="{{ route('admin.lines.destroy', $line->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE') <button class="btn btn-sm btn-danger" data-confirm="Yakin ingin menghapus line ini?" data-confirm-danger><i class="ph ph-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <div class="modal fade" id="editModal{{ $line->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.lines.update', $line->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header"><h5 class="modal-title fw-bold">Edit Line</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <div class="mb-3"><label class="form-label text-muted fw-bold">Nama Line</label><input type="text" name="name" class="form-control" value="{{ $line->name }}" required></div>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($lines->isEmpty()) <tr><td colspan="3" class="text-center text-muted">Belum ada data line.</td></tr> @endif
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.lines.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title fw-bold">Tambah Line</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label text-muted fw-bold">Nama Line</label><input type="text" name="name" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
