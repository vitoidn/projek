@extends('layouts.dashboard')

@section('title', 'Operator Dashboard')
@section('page-title', 'Operator Dashboard')

@push('styles')
<style>
    .stat-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 20px;
        background: #fff;
        transition: border-color 0.15s ease;
    }
    .stat-card:hover {
        border-color: #cbd5e1;
    }
    .stat-card .stat-icon {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.4rem;
    }
    .stat-card .stat-value {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1.2;
        color: #0f172a;
    }
    .stat-card .stat-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
</style>
@endpush

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="ph ph-calendar"></i></div>
            <div>
                <div class="stat-value">{{ $todayRecords }}</div>
                <div class="stat-label">Records Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="ph ph-file-draft"></i></div>
            <div>
                <div class="stat-value">{{ $draftRecords }}</div>
                <div class="stat-label">Draft</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="ph ph-list-dashes"></i></div>
            <div>
                <div class="stat-value">{{ $todayBodyStats->total_rows }}</div>
                <div class="stat-label">Body Rows Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="ph ph-clock"></i></div>
            <div>
                <div class="stat-value">{{ intdiv($todayBodyStats->total_min, 60) }}j {{ $todayBodyStats->total_min % 60 }}m</div>
                <div class="stat-label">Total Jam Hari Ini</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="ph ph-clock-counter-clockwise text-primary me-2"></i> Riwayat Record Saya
                </h5>
                <a href="{{ route('operator.op-record.create') }}" class="btn btn-primary fw-bold">
                    <i class="ph ph-plus-circle me-2"></i> Buat Record Baru
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="ph ph-check-circle me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Process</th>
                            <th>Shift</th>
                            <th>NIK</th>
                            <th>Status</th>
                            <th>Rows</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myRecords as $rec)
                        <tr>
                            <td class="fw-bold">{{ \Carbon\Carbon::parse($rec->date)->format('d M Y') }}</td>
                            <td><span class="badge bg-primary">{{ $rec->process_main }}</span></td>
                            <td>{{ $rec->shift->name ?? '-' }}</td>
                            <td><small class="text-muted">{{ $rec->nik_list }}</small></td>
                            <td>
                                @if($rec->status == 'draft')
                                    <span class="badge bg-warning text-dark">Draft</span>
                                @else
                                    <span class="badge bg-success">Final</span>
                                @endif
                            </td>
                            <td>{{ $rec->bodies->count() }}</td>
                            <td class="text-end">
                                <a href="{{ route('operator.op-record.show', $rec->id) }}" class="btn btn-sm btn-info text-white" title="Lihat">
                                    <i class="ph ph-eye"></i>
                                </a>
                                @if($rec->status == 'draft')
                                    <a href="{{ route('operator.op-record.edit', $rec->id) }}" class="btn btn-sm btn-warning text-white" title="Edit Header">
                                        <i class="ph ph-pencil"></i>
                                    </a>
                                    <a href="{{ route('operator.op-record.index') }}#record-{{ $rec->id }}" class="btn btn-sm btn-success text-white" title="Isi Body">
                                        <i class="ph ph-list-plus"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        @if($myRecords->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="ph ph-tray fa-2x mb-2 d-block"></i>
                                Belum ada record. <a href="{{ route('operator.op-record.create') }}">Buat baru</a>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
