@extends('layouts.dashboard')

@section('title', 'Report Record')
@section('page-title', 'Report Record')

@section('content')
<div class="glass-card">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3 no-print">
        <div>
            <h5 class="fw-bold mb-0">
                <i class="ph ph-file-text text-primary me-2"></i>
                Operational Record #{{ $record->id }}
            </h5>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('operator.report-record.index') }}" class="btn btn-light fw-bold">
                <i class="ph ph-arrow-left me-1"></i> Kembali
            </a>
            <button class="btn btn-primary fw-bold" onclick="window.print()">
                <i class="ph ph-printer me-2"></i> Print / PDF
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <small class="text-muted d-block">Date</small>
            <span class="fw-bold">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</span>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Shift</small>
            <span class="fw-bold">{{ $record->shift->name ?? '-' }}</span>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Process Main</small>
            <span class="fw-bold">{{ $record->process_main }}</span>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Process 2</small>
            <span class="fw-bold">{{ $record->process2_list ?: '-' }}</span>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">NIK / Karyawan</small>
            <span class="fw-bold">{{ $record->nik_list }}</span>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Status</small>
            <span class="fw-bold">{!! $record->status == 'final' ? '<span class="badge bg-success">Final</span>' : '<span class="badge bg-warning text-dark">Draft</span>' !!}</span>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Created By</small>
            <span class="fw-bold">{{ $record->createdBy->name ?? '-' }}</span>
        </div>
        <div class="col-md-3">
            <small class="text-muted d-block">Signature</small>
            <span class="fw-bold">{{ $record->prepare_signature ?: '-' }}</span>
        </div>
    </div>

    <h6 class="fw-bold mt-4 mb-3"><i class="ph ph-list-bullets text-primary me-2"></i> Detail Aktivitas</h6>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Part Code</th>
                    <th>LOT</th>
                    <th>Activity Code</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Min</th>
                    <th>QTY</th>
                    <th>NG</th>
                    <th>Hold</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                @forelse($record->bodies as $i => $body)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $body->part_code ?: '-' }}</td>
                    <td>{{ $body->lot_id ?: '-' }}</td>
                    <td>{{ $body->code->code ?? '-' }} - {{ $body->code->name ?? '' }}</td>
                    <td>{{ $body->start_time ? \Carbon\Carbon::parse($body->start_time)->format('H:i') : '-' }}</td>
                    <td>{{ $body->end_time ? \Carbon\Carbon::parse($body->end_time)->format('H:i') : '-' }}</td>
                    <td>{{ $body->duration_min }}</td>
                    <td>{{ $body->qty }}</td>
                    <td>{{ $body->ng }}</td>
                    <td>{{ $body->hold }}</td>
                    <td>{{ $body->remark ?: '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">Belum ada body rows</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="6" class="text-end">Total</td>
                    <td>{{ $record->bodies->sum('duration_min') }}</td>
                    <td>{{ $record->bodies->sum('qty') }}</td>
                    <td>{{ $record->bodies->sum('ng') }}</td>
                    <td>{{ $record->bodies->sum('hold') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('styles')
<style>
    @@media print {
        .sidebar, .top-navbar, .no-print { display: none !important; }
        .glass-card { box-shadow: none !important; border: 1px solid #ddd !important; }
        .table { font-size: 11px; }
        .badge { border: 1px solid #000 !important; }
    }
</style>
@endpush
@endSection
