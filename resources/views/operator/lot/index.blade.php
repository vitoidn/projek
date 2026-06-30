@extends('layouts.dashboard')
@section('title', 'Daftar Lot Produksi')
@section('page-title')
    O/R Header: {{ \Carbon\Carbon::parse($record->date)->format('d M Y') }} - {{ $record->line->name }} - {{ $record->shift->name }}
@endsection

@section('content')
<div class="row g-4">
    <!-- Form Tambah Lot -->
    <div class="col-12 col-lg-4">
        <div class="glass-card h-100">
            <h5 class="fw-bold mb-4"><i class="fas fa-plus-circle text-primary me-2"></i> Tambah Lot Baru</h5>
            <form action="{{ route('operator.lot.store', $record->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-muted fw-bold">Pilih Part</label>
                    <select name="part_id" class="form-select" required>
                        <option value="">-- Pilih Part --</option>
                        @foreach($parts as $part)
                            <option value="{{ $part->id }}">{{ $part->part_code }} ({{ $part->qty_per_lot }} pcs/lot)</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold">Nomor Lot</label>
                    <input type="text" name="lot_number" class="form-control" placeholder="Contoh: LOT-001" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold py-2"><i class="fas fa-play me-2"></i> Buat & Buka Lot</button>
            </form>
        </div>
    </div>

    <!-- Riwayat Lot -->
    <div class="col-12 col-lg-8">
        <div class="glass-card h-100">
            <h5 class="fw-bold mb-4"><i class="fas fa-history text-primary me-2"></i> Riwayat Lot Hari Ini</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Part Code</th>
                            <th>Lot Number</th>
                            <th>Status</th>
                            <th>Qty OK</th>
                            <th>Qty NG</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lots as $lot)
                        <tr>
                            <td class="fw-bold">{{ $lot->part->part_code }}</td>
                            <td>{{ $lot->lot_number }}</td>
                            <td>
                                @if($lot->status == 'Ready')
                                    <span class="badge bg-secondary">Ready</span>
                                @elseif($lot->status == 'Running')
                                    <span class="badge bg-primary">Running</span>
                                @elseif($lot->status == 'Paused')
                                    <span class="badge bg-warning text-dark">Paused</span>
                                @else
                                    <span class="badge bg-success">Finished</span>
                                @endif
                            </td>
                            <td class="text-success fw-bold">{{ $lot->qty_ok }}</td>
                            <td class="text-danger fw-bold">{{ $lot->qty_ng }}</td>
                            <td class="text-end">
                                <a href="{{ route('operator.lot.execute', ['or_id' => $record->id, 'id' => $lot->id]) }}" class="btn btn-sm btn-info text-white">
                                    Buka <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @if($lots->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada lot yang dikerjakan.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
