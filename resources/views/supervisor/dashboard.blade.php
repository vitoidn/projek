@extends('layouts.dashboard')

@section('title', 'Supervisor Dashboard')
@section('page-title', 'Supervisor Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="glass-card text-center p-4">
            <small class="text-muted fw-bold">Today's Records</small>
            <h2 class="fw-bold mb-0 text-primary">{{ $recordsToday }}</h2>
            <small>Final: {{ $recordsFinalToday }}</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card text-center p-4">
            <small class="text-muted fw-bold">Today's QTY</small>
            <h2 class="fw-bold mb-0 text-success">{{ number_format($todaySummary->total_qty ?? 0) }}</h2>
            <small>NG: <span class="text-danger">{{ number_format($todaySummary->total_ng ?? 0) }}</span></small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card text-center p-4">
            <small class="text-muted fw-bold">Today's Duration</small>
            <h2 class="fw-bold mb-0 text-info">{{ $todaySummary->total_duration_min ?? 0 }}</h2>
            <small>minutes</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="glass-card">
            <h5 class="fw-bold border-bottom pb-3 mb-3"><i class="ph ph-clock text-primary me-2"></i> Recent Operational Records</h5>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Process Main</th>
                            <th>Process 2</th>
                            <th>Shift</th>
                            <th>NIK</th>
                            <th>Created By</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRecords as $rec)
                        <tr>
                            <td class="fw-bold">{{ \Carbon\Carbon::parse($rec->date)->format('d M Y') }}</td>
                            <td>{{ $rec->process_main }}</td>
                            <td><small>{{ $rec->process2_list ?: '-' }}</small></td>
                            <td>{{ $rec->shift->name ?? '-' }}</td>
                            <td>{{ $rec->nik_list }}</td>
                            <td>{{ $rec->createdBy->name ?? '-' }}</td>
                            <td>{!! $rec->status == 'final' ? '<span class="badge bg-success">Final</span>' : '<span class="badge bg-warning text-dark">Draft</span>' !!}</td>
                            <td class="text-end">
                                <a href="{{ route('supervisor.op-record.show', $rec->id) }}" class="btn btn-sm btn-info text-white"><i class="ph ph-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endSection