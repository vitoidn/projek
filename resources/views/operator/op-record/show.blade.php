@extends('layouts.dashboard')

@section('title', 'Detail Operational Record')
@section('page-title')
    <a href="{{ route('operator.op-record.index') }}" class="btn btn-sm btn-light me-2"><i class="ph ph-arrow-left"></i></a>
    Record Detail - {{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card mb-4">
            <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                <h5 class="fw-bold"><i class="ph ph-info text-primary me-2"></i> Header Info</h5>
                <div>
                    @if($record->status == 'draft')
                        <span class="badge bg-warning text-dark fs-6">Draft</span>
                    @else
                        <span class="badge bg-success fs-6">Final</span>
                    @endif
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-3"><strong>Date:</strong> {{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</div>
                <div class="col-md-3"><strong>Shift:</strong> {{ $record->shift->name ?? '-' }}</div>
                <div class="col-md-3"><strong>Process Main:</strong> {{ $record->process_main }}</div>
                <div class="col-md-3"><strong>Process 2:</strong> {{ $record->process2_list ?: '-' }}</div>
                <div class="col-md-4"><strong>NIK(s):</strong>
                    <small class="text-muted">{{ $record->nik_list }}</small>
                </div>
                <div class="col-md-4"><strong>Created By:</strong> {{ $record->createdBy->name ?? '-' }}</div>
                <div class="col-md-4"><strong>Status:</strong> {{ ucfirst($record->status) }}</div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-3">
            @if($record->status == 'draft')
                <a href="{{ route('operator.op-record.edit', $record->id) }}" class="btn btn-warning fw-bold">
                    <i class="ph ph-pencil me-2"></i> Edit Record
                </a>
                <form action="{{ route('operator.op-record.submit', $record->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success fw-bold"
                        data-confirm="Submit record sebagai Final?"
                        data-confirm-yes="Ya, Finalkan">
                        <i class="ph ph-check-circle me-2"></i> Submit Final
                    </button>
                </form>
            @endif
            <a href="{{ route('operator.report-record.preview', $record->id) }}" class="btn btn-secondary fw-bold">
                <i class="ph ph-file-arrow-down me-2"></i> Report
            </a>
        </div>

        <div class="glass-card">
            <h5 class="fw-bold mb-3"><i class="ph ph-table text-primary me-2"></i> Detail Aktivitas (Body)</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Part Code</th>
                            <th>LOT</th>
                            <th>Code</th>
                            <th>Aktivitas</th>
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
                        @foreach($record->bodies as $i => $body)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="fw-bold">{{ $body->part_code }}</td>
                            <td>{{ $body->lot_id ?: '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $body->code->code ?? '-' }}</span></td>
                            <td>{{ $body->code->name ?? '-' }}</td>
                            <td>{{ $body->start_time }}</td>
                            <td>{{ $body->end_time }}</td>
                            <td class="fw-bold">{{ $body->duration_min }} min</td>
                            <td>{{ number_format($body->qty) }}</td>
                            <td class="text-danger">{{ number_format($body->ng) }}</td>
                            <td class="text-warning">{{ number_format($body->hold) }}</td>
                            <td><small>{{ $body->remark ?? '-' }}</small></td>
                        </tr>
                        @endforeach
                        @if($record->bodies->isEmpty())
                        <tr>
                            <td colspan="12" class="text-center text-muted">Belum ada data Body.</td>
                        </tr>
                        @endif
                    </tbody>
                    @if($record->bodies->isNotEmpty())
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="7" class="text-end">Total:</td>
                            <td>{{ $record->bodies->sum('duration_min') }} min</td>
                            <td>{{ number_format($record->bodies->sum('qty')) }}</td>
                            <td class="text-danger">{{ number_format($record->bodies->sum('ng')) }}</td>
                            <td class="text-warning">{{ number_format($record->bodies->sum('hold')) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endSection