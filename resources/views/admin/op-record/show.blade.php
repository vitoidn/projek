@extends('layouts.dashboard')

@section('title', 'Detail Operational Record')
@section('page-title')
    <a href="{{ route('admin.op-record.index') }}" class="btn btn-sm btn-light me-2"><i class="ph ph-arrow-left"></i></a>
    Record Detail
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
                <div class="col-md-2"><strong>Date:</strong> {{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</div>
                <div class="col-md-2"><strong>Shift:</strong> {{ $record->shift->name ?? '-' }}</div>
                <div class="col-md-2"><strong>Process Main:</strong> {{ $record->process_main }}</div>
                <div class="col-md-3"><strong>Process 2:</strong> {{ $record->process2_list ?: '-' }}</div>
                <div class="col-md-3"><strong>NIK(s):</strong>
                    <small class="text-muted">{{ $record->nik_list }}</small>
                </div>
                <div class="col-md-3"><strong>Created By:</strong> {{ $record->createdBy->name ?? '-' }}</div>
                <div class="col-md-3"><strong>Signature:</strong> {{ $record->prepare_signature ? 'Ada' : '-' }}</div>
            </div>
        </div>

        <div class="glass-card">
            <h5 class="fw-bold border-bottom pb-3 mb-3"><i class="ph ph-table text-primary me-2"></i> Detail Body</h5>
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
                    </tbody>
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
                </table>
            </div>
        </div>
    </div>
</div>
@endSection