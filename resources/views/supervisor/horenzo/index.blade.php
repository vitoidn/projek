@extends('layouts.dashboard')
@section('title', 'Horenzo Generator')
@section('page-title', 'Horenzo Generator')

@section('content')
<div class="row g-4">
    <div class="col-12 col-md-4">
        <div class="glass-card mb-4 h-100">
            <h5 class="fw-bold mb-4"><i class="fas fa-filter text-primary me-2"></i> Filter Horenzo</h5>
            <form action="{{ route('supervisor.horenzo.index') }}" method="GET">
                <div class="mb-3">
                    <label class="form-label text-muted fw-bold">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date', date('Y-m-d')) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted fw-bold">Line</label>
                    <select name="line_id" class="form-select" required>
                        <option value="">-- Pilih Line --</option>
                        @foreach($lines as $line)
                            <option value="{{ $line->id }}" {{ request('line_id') == $line->id ? 'selected' : '' }}>{{ $line->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold">Shift</label>
                    <select name="shift_id" class="form-select" required>
                        <option value="">-- Pilih Shift --</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="fas fa-sync me-2"></i> Generate Horenzo</button>
            </form>
        </div>
    </div>

    <div class="col-12 col-md-8">
        @if(isset($horenzo))
        <div class="glass-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-file-alt me-2"></i> Laporan Horenzo</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="fas fa-print me-2"></i> Print</button>
            </div>
            
            <div class="row text-center mb-4 g-3">
                <div class="col-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <small class="text-muted d-block fw-bold mb-1">Tanggal</small>
                        <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($horenzo->date)->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <small class="text-muted d-block fw-bold mb-1">Line</small>
                        <span class="fw-bold text-dark">{{ $horenzo->line->name }}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <small class="text-muted d-block fw-bold mb-1">Shift</small>
                        <span class="fw-bold text-dark">{{ $horenzo->shift->name }}</span>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="p-4 border rounded border-primary bg-light-primary text-center h-100">
                        <h6 class="fw-bold text-primary mb-3">Achievement Produksi</h6>
                        <h1 class="display-3 fw-bold mb-0 text-primary">{{ $horenzo->achievement_percent }}%</h1>
                        <p class="text-muted mt-2 mb-0">Total Target: {{ number_format($horenzo->target_qty) }} pcs</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 border rounded bg-light h-100">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Rincian Hasil</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Qty Produksi</span>
                            <span class="fw-bold">{{ number_format($horenzo->total_production) }} pcs</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Qty OK (Good)</span>
                            <span class="fw-bold text-success">{{ number_format($horenzo->total_ok) }} pcs</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Qty NG (Defect)</span>
                            <span class="fw-bold text-danger">{{ number_format($horenzo->total_ng) }} pcs</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Waktu Downtime</span>
                            <span class="fw-bold text-warning">{{ gmdate("H:i:s", $horenzo->total_downtime_sec) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @else
        <div class="glass-card h-100 d-flex flex-column align-items-center justify-content-center text-muted">
            <i class="fas fa-file-invoice fa-4x mb-3 text-light"></i>
            <h5>Silakan pilih filter di samping untuk melihat Horenzo.</h5>
        </div>
        @endif
    </div>
</div>
@endsection
