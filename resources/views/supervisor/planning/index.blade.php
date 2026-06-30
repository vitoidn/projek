@extends('layouts.dashboard')
@section('title', 'Production Planning')
@section('page-title', 'Production Planning')

@section('content')
<div class="glass-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Production Planning</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal"><i class="ph ph-plus"></i> Buat Planning</button>
    </div>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">Terjadi kesalahan pada input data.</div> @endif
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Shift</th>
                    <th>Line</th>
                    <th>Part Code</th>
                    <th>Target Qty</th>
                    <th>Target Lot</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plannings as $key => $planning)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td class="fw-bold">{{ \Carbon\Carbon::parse($planning->date)->format('d M Y') }}</td>
                    <td>{{ $planning->shift->name }}</td>
                    <td>{{ $planning->line->name }}</td>
                    <td>{{ $planning->part->part_code }}</td>
                    <td class="fw-bold text-primary">{{ number_format($planning->target_qty) }} pcs</td>
                    <td class="fw-bold text-success">{{ $planning->jumlah_lot }} Lot</td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $planning->id }}"><i class="ph ph-pencil"></i></button>
                        <form action="{{ route('supervisor.planning.destroy', $planning->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE') <button class="btn btn-sm btn-danger" data-confirm="Yakin hapus planning ini?" data-confirm-danger><i class="ph ph-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <div class="modal fade" id="editModal{{ $planning->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('supervisor.planning.update', $planning->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="modal-header"><h5 class="modal-title fw-bold">Edit Planning</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <div class="mb-3"><label class="form-label text-muted fw-bold">Tanggal</label><input type="date" name="date" class="form-control" value="{{ $planning->date }}" required></div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted fw-bold">Shift</label>
                                        <select name="shift_id" class="form-select" required>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}" {{ $planning->shift_id == $shift->id ? 'selected' : '' }}>{{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted fw-bold">Line</label>
                                        <select name="line_id" class="form-select" required>
                                            @foreach($lines as $line)
                                                <option value="{{ $line->id }}" {{ $planning->line_id == $line->id ? 'selected' : '' }}>{{ $line->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted fw-bold">Part</label>
                                        <select name="part_id" class="form-select" required>
                                            @foreach($parts as $part)
                                                <option value="{{ $part->id }}" {{ $planning->part_id == $part->id ? 'selected' : '' }}>{{ $part->part_code }} ({{ $part->qty_per_lot }} pcs/lot)</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3"><label class="form-label text-muted fw-bold">Target Qty (pcs)</label><input type="number" name="target_qty" class="form-control" value="{{ $planning->target_qty }}" required min="1"></div>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($plannings->isEmpty()) <tr><td colspan="8" class="text-center text-muted">Belum ada data planning.</td></tr> @endif
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('supervisor.planning.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title fw-bold">Tambah Planning Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label text-muted fw-bold">Tanggal</label><input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Shift</label>
                        <select name="shift_id" class="form-select" required>
                            <option value="">-- Pilih Shift --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }} ({{ substr($shift->start_time,0,5) }} - {{ substr($shift->end_time,0,5) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Line</label>
                        <select name="line_id" class="form-select" required>
                            <option value="">-- Pilih Line --</option>
                            @foreach($lines as $line)
                                <option value="{{ $line->id }}">{{ $line->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Part</label>
                        <select name="part_id" class="form-select" required>
                            <option value="">-- Pilih Part --</option>
                            @foreach($parts as $part)
                                <option value="{{ $part->id }}">{{ $part->part_code }} ({{ $part->qty_per_lot }} pcs/lot)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label text-muted fw-bold">Target Qty (pcs)</label><input type="number" name="target_qty" class="form-control" required min="1"></div>
                    <div class="alert alert-info py-2 mb-0"><small><i class="ph ph-info"></i> Sistem akan otomatis menghitung Target Lot berdasarkan (Target Qty / Qty per Lot part).</small></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
