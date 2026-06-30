@extends('layouts.dashboard')

@section('title', 'Operational Records')
@section('page-title', 'All Operational Records')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card">
            <h5 class="fw-bold border-bottom pb-3 mb-3">
                <i class="ph ph-clipboard-text text-primary me-2"></i> Daftar Record
            </h5>

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
                            <th>Rows</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $rec)
                        <tr>
                            <td class="fw-bold">{{ \Carbon\Carbon::parse($rec->date)->format('d M Y') }}</td>
                            <td>{{ $rec->process_main }}</td>
                            <td><small>{{ $rec->process2_list ?: '-' }}</small></td>
                            <td>{{ $rec->shift->name ?? '-' }}</td>
                            <td><small class="text-muted">{{ $rec->nik_list }}</small></td>
                            <td>{{ $rec->createdBy->name ?? '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $rec->bodies->count() }}</span></td>
                            <td>
                                @if($rec->status == 'draft')
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @else
                                    <span class="badge bg-success">Final</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('supervisor.op-record.show', $rec->id) }}" class="btn btn-sm btn-info text-white"><i class="ph ph-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $records->links() }}</div>
        </div>
    </div>
</div>
@endSection