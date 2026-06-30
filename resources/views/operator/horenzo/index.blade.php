@extends('layouts.dashboard')

@section('title', 'Horenzo Reports')
@section('page-title', 'Laporan Horenzo')

@section('content')
<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="glass-card h-100">
            <h5 class="fw-bold mb-4"><i class="ph ph-funnel text-primary me-2"></i> Filter & Generate</h5>
            <form action="{{ route('operator.horenzo.generate') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold small">Date Range</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from', date('Y-m-01')) }}" required>
                        </div>
                        <div class="col-6">
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Shift</label>
                    <select name="shift_id" class="form-select form-select-sm">
                        <option value="">All Shifts</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Process Main</label>
                    <select name="process_main" class="form-select form-select-sm">
                        <option value="">All Processes</option>
                        @foreach($processMains as $pm)
                            <option value="{{ $pm }}" {{ request('process_main') == $pm ? 'selected' : '' }}>{{ $pm }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Part Code</label>
                    <select name="part_code" class="form-select form-select-sm">
                        <option value="">All Parts</option>
                        @foreach($partCodes as $pc)
                            <option value="{{ $pc->code }}" {{ request('part_code') == $pc->code ? 'selected' : '' }}>{{ $pc->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">LOT Number</label>
                    <select name="lot_id" class="form-select form-select-sm">
                        <option value="">All LOTs</option>
                        @foreach($lotNumbers as $lot)
                            <option value="{{ $lot->id }}" {{ request('lot_id') == $lot->id ? 'selected' : '' }}>{{ $lot->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold small">NIK (keyword)</label>
                    <input type="text" name="nik" class="form-control form-control-sm" placeholder="Cari NIK" value="{{ request('nik') }}">
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold">
                    <i class="ph ph-arrows-clockwise me-2"></i> Generate
                </button>
            </form>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        @if(session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif

        @if(isset($report))
            @php $data = $report->snapshot_data; @endphp
            <div class="glass-card mb-4">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                    <h5 class="fw-bold mb-0"><i class="ph ph-file-text text-primary me-2"></i> Laporan Horenzo</h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="ph ph-printer me-2"></i> Print</button>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded shadow-sm text-center">
                            <small class="text-muted d-block fw-bold">Total QTY</small>
                            <span class="fs-3 fw-bold text-dark">{{ number_format($data['summary']->total_qty ?? 0) }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded shadow-sm text-center">
                            <small class="text-muted d-block fw-bold">Total NG</small>
                            <span class="fs-3 fw-bold text-danger">{{ number_format($data['summary']->total_ng ?? 0) }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded shadow-sm text-center">
                            <small class="text-muted d-block fw-bold">Total Hold</small>
                            <span class="fs-3 fw-bold text-warning">{{ number_format($data['summary']->total_hold ?? 0) }}</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded shadow-sm text-center">
                            <small class="text-muted d-block fw-bold">Total Duration</small>
                            <span class="fs-3 fw-bold text-primary">{{ $data['summary']->total_duration_min ?? 0 }} min</span>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($data['per_code']) && count($data['per_code']) > 0)
            <div class="glass-card mb-4">
                <h5 class="fw-bold border-bottom pb-3 mb-3"><i class="ph ph-chart-bar text-primary me-2"></i> Breakdown per Aktivitas</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Aktivitas</th>
                                <th>Total Records</th>
                                <th>Total Durasi (min)</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalDur = $data['summary']->total_duration_min ?? 1; @endphp
                            @foreach($data['per_code'] as $row)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $row->activity_code }}</span></td>
                                <td>{{ $row->activity_name }}</td>
                                <td>{{ $row->total_records }}</td>
                                <td class="fw-bold">{{ $row->total_duration_min }}</td>
                                <td>
                                    <div class="progress" style="height:20px">
                                        <div class="progress-bar" role="progressbar" style="width: {{ ($row->total_duration_min / $totalDur) * 100 }}%">
                                            {{ round(($row->total_duration_min / $totalDur) * 100, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if(!empty($data['per_part']) && count($data['per_part']) > 0)
            <div class="glass-card">
                <h5 class="fw-bold border-bottom pb-3 mb-3"><i class="ph ph-package text-primary me-2"></i> Breakdown per Part</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Part Code</th>
                                <th>Total QTY</th>
                                <th>Total NG</th>
                                <th>NG Rate</th>
                                <th>Total Durasi (min)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['per_part'] as $row)
                            <tr>
                                <td class="fw-bold">{{ $row->part_code }}</td>
                                <td>{{ number_format($row->total_qty) }}</td>
                                <td class="text-danger">{{ number_format($row->total_ng) }}</td>
                                <td>{{ $row->total_qty > 0 ? round(($row->total_ng / $row->total_qty) * 100, 1) : 0 }}%</td>
                                <td>{{ $row->total_duration_min }} min</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        @else
            <div class="glass-card h-100 d-flex flex-column align-items-center justify-content-center text-muted py-5">
                <i class="ph ph-file-invoice fa-4x mb-3 text-light"></i>
                <h5>Pilih filter dan klik Generate untuk melihat laporan Horenzo.</h5>
            </div>
        @endif
    </div>
</div>
@endSection
