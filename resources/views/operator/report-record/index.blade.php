@extends('layouts.dashboard')

@section('title', 'Report All Records')
@section('page-title', 'Report All Records')

@section('content')
<div class="glass-card">
    <h5 class="fw-bold mb-4"><i class="ph ph-export text-primary me-2"></i> Export Operational Record</h5>
    <p class="text-muted small mb-4">Pilih record untuk melihat report & export per header.</p>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Process Main</th>
                    <th>Process 2</th>
                    <th>Shift</th>
                    <th>NIK</th>
                    <th>Body Rows</th>
                    <th>Status</th>
                    <th class="text-end">Report</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $rec)
                <tr>
                    <td class="fw-bold">{{ \Carbon\Carbon::parse($rec->date)->format('d M Y') }}</td>
                    <td><span class="badge bg-primary">{{ $rec->process_main }}</span></td>
                    <td><small>{{ $rec->process2_list ?: '-' }}</small></td>
                    <td>{{ $rec->shift->name ?? '-' }}</td>
                    <td><small class="text-muted">{{ $rec->nik_list }}</small></td>
                    <td>{{ $rec->bodies_count }}</td>
                    <td>{!! $rec->status == 'final' ? '<span class="badge bg-success">Final</span>' : '<span class="badge bg-warning text-dark">Draft</span>' !!}</td>
                    <td class="text-end">
                        <a href="{{ route('operator.report-record.preview', $rec->id) }}" class="btn btn-sm btn-primary fw-bold" title="Lihat & Export Report">
                            <i class="ph ph-file-arrow-down me-1"></i> Report
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Belum ada record.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $records->links() }}
    </div>
</div>
@endSection
