@extends('layouts.dashboard')

@section('title', 'Horenzo Generator')
@section('page-title', 'Horenzo Generator')

@section('content')
<div class="row g-4">
    <div class="col-12 col-lg-4">
        <div class="glass-card h-100">
            <h5 class="fw-bold mb-4"><i class="ph ph-funnel text-primary me-2"></i> Filter & Generate</h5>
            <form action="{{ route('supervisor.horenzo.generate') }}" method="POST">
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

            <div class="glass-card mb-4 no-print">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="ph ph-file-text text-primary me-2"></i> Laporan Horenzo</h5>
                    <button class="btn btn-outline-secondary fw-bold btn-sm" onclick="window.print()"><i class="ph ph-printer me-2"></i> Print</button>
                </div>
            </div>

            <div class="report-container">
                <div class="report-header">
                    <div class="report-header-left">
                        <img src="{{ asset('usui-logo.png') }}" alt="PT USUI" class="report-logo">
                        <div>
                            <div class="report-company">PT USUI</div>
                            <div class="report-company-desc">Manufacturing Division</div>
                        </div>
                    </div>
                    <div class="report-header-right">
                        <table class="doc-info">
                            <tr>
                                <td class="doc-info-label">Form No.</td>
                                <td class="doc-info-value">QP-04002-0014-2</td>
                            </tr>
                            <tr>
                                <td class="doc-info-label">Period</td>
                                <td class="doc-info-value">{{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }} ~ {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="report-title">
                    HORENZO REPORT
                </div>

                <table class="report-info-table">
                    <tr>
                        <td class="report-info-label">Period</td>
                        <td class="report-info-value">: {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }} - {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}</td>
                        <td class="report-info-label">Generated By</td>
                        <td class="report-info-value">: {{ auth()->user()->name ?? '-' }}</td>
                    </tr>
                </table>

                <div class="report-summary-row">
                    <div class="report-summary-item">
                        <div class="summary-label">Total QTY</div>
                        <div class="summary-value">{{ number_format($data['summary']->total_qty ?? 0) }}</div>
                    </div>
                    <div class="report-summary-item">
                        <div class="summary-label">Total NG</div>
                        <div class="summary-value summary-value-danger">{{ number_format($data['summary']->total_ng ?? 0) }}</div>
                    </div>
                    <div class="report-summary-item">
                        <div class="summary-label">NG Rate</div>
                        <div class="summary-value">@php $totalQ = $data['summary']->total_qty ?? 0; $totalNg = $data['summary']->total_ng ?? 0; @endphp {{ $totalQ > 0 ? number_format(($totalNg / $totalQ) * 100, 1) : 0 }}%</div>
                    </div>
                    <div class="report-summary-item">
                        <div class="summary-label">Total Hold</div>
                        <div class="summary-value">{{ number_format($data['summary']->total_hold ?? 0) }}</div>
                    </div>
                    <div class="report-summary-item">
                        <div class="summary-label">Total Duration</div>
                        <div class="summary-value">{{ $data['summary']->total_duration_min ?? 0 }} min</div>
                    </div>
                </div>

                @if(!empty($data['nik_per_process']))
                @php
                    $roleLabels = [
                        'bending' => 'Bending',
                        'shape_check_jig' => 'Shape Check Jig',
                        'drawing_inspection' => 'Drawing & Inspection',
                        'drawing' => 'Drawing',
                        'inspection' => 'Inspection',
                    ];
                @endphp
                <div class="report-section-title">Personnel by Process</div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Process</th>
                            <th>NIK / Personnel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['nik_per_process'] as $groupKey => $niks)
                        @php
                            $parts = explode('|', $groupKey);
                            $pm = $parts[0];
                            $role = $parts[1] ?? '';
                            $display = $roleLabels[$role] ?? ucwords(str_replace('_', ' ', $role));
                            $display .= ' (' . $pm . ')';
                        @endphp
                        <tr>
                            <td class="fw-bold">{{ $display }}</td>
                            <td>{{ implode(' | ', $niks) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                @if(!empty($data['per_code']) && count($data['per_code']) > 0)
                <div class="report-section-title">Breakdown by Activity</div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Activity</th>
                            <th>Total Records</th>
                            <th>Total Duration (min)</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalDur = $data['summary']->total_duration_min ?? 1; @endphp
                        @foreach($data['per_code'] as $row)
                        <tr>
                            <td class="text-center">{{ $row->activity_code }}</td>
                            <td>{{ $row->activity_name }}</td>
                            <td class="text-center">{{ $row->total_records }}</td>
                            <td class="text-center fw-bold">{{ $row->total_duration_min }}</td>
                            <td class="text-center">{{ round(($row->total_duration_min / $totalDur) * 100, 1) }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                @if(!empty($data['per_part']) && count($data['per_part']) > 0)
                <div class="report-section-title">Breakdown by Part</div>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Part Code</th>
                            <th>Total QTY</th>
                            <th>Total NG</th>
                            <th>NG Rate</th>
                            <th>Total Duration (min)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['per_part'] as $row)
                        <tr>
                            <td class="fw-bold">{{ $row->part_code }}</td>
                            <td class="text-center">{{ number_format($row->total_qty) }}</td>
                            <td class="text-center">{{ number_format($row->total_ng) }}</td>
                            <td class="text-center">{{ $row->total_qty > 0 ? round(($row->total_ng / $row->total_qty) * 100, 1) : 0 }}%</td>
                            <td class="text-center">{{ $row->total_duration_min }} min</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                <div class="report-signatures">
                    <div class="signature-block">
                        <div class="signature-label">Prepared by,</div>
                        <div class="signature-space"></div>
                        <div class="signature-name">{{ auth()->user()->name ?? '______________________' }}</div>
                    </div>
                    <div class="signature-block">
                        <div class="signature-label">Checked by,</div>
                        <div class="signature-space"></div>
                        <div class="signature-name">______________________</div>
                    </div>
                    <div class="signature-block">
                        <div class="signature-label">Approved by,</div>
                        <div class="signature-space"></div>
                        <div class="signature-name">______________________</div>
                    </div>
                </div>

                <div class="report-footer">
                    Printed on: {{ now()->format('d M Y H:i') }} | Page <span class="page-number">1</span>
                </div>
            </div>
        @else
            <div class="glass-card h-100 d-flex flex-column align-items-center justify-content-center text-muted py-5">
                <i class="ph ph-file-invoice" style="font-size:3rem; opacity:0.3; margin-bottom:16px;"></i>
                <h5>Pilih filter dan klik Generate untuk melihat laporan Horenzo.</h5>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .no-print {
        margin-bottom: 20px;
    }

    .report-container {
        background: #fff;
        border: 1px solid #d0d0d0;
        padding: 30px 35px;
        font-family: 'Courier New', Courier, monospace;
        font-size: 11px;
        color: #000;
        line-height: 1.5;
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 15px;
        border-bottom: 2px solid #000;
        margin-bottom: 15px;
    }

    .report-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .report-logo {
        height: 45px;
        width: auto;
    }

    .report-company {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    .report-company-desc {
        font-size: 10px;
        color: #555;
    }

    .report-header-right .doc-info {
        border-collapse: collapse;
    }

    .doc-info td {
        padding: 1px 0 1px 10px;
        font-size: 10px;
    }

    .doc-info-label {
        font-weight: 700;
        text-align: right;
        color: #444;
    }

    .doc-info-value {
        text-align: left;
        min-width: 80px;
    }

    .report-title {
        text-align: center;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 2px;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid #999;
    }

    .report-info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        font-size: 11px;
    }

    .report-info-table td {
        padding: 3px 8px;
        border: none;
    }

    .report-info-label {
        font-weight: 700;
        width: 110px;
        color: #333;
    }

    .report-info-value {
        width: 200px;
    }

    .report-summary-row {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
    }

    .report-summary-item {
        flex: 1;
        border: 1px solid #ccc;
        text-align: center;
        padding: 10px 5px;
        background: #fafafa;
    }

    .summary-label {
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #555;
        margin-bottom: 4px;
    }

    .summary-value {
        font-size: 16px;
        font-weight: 700;
    }

    .summary-value-danger {
        color: #b91c1c;
    }

    .report-section-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 1px solid #999;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 10px;
    }

    .report-table thead th {
        background: #e0e0e0;
        border: 1px solid #999;
        padding: 6px 4px;
        text-align: center;
        font-weight: 700;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .report-table tbody td {
        border: 1px solid #ccc;
        padding: 4px 6px;
        vertical-align: middle;
    }

    .report-table tbody tr:nth-child(even) {
        background: #f8f8f8;
    }

    .report-signatures {
        display: flex;
        justify-content: space-between;
        margin: 30px 20px 15px;
    }

    .signature-block {
        text-align: center;
        min-width: 150px;
    }

    .signature-label {
        font-size: 10px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #444;
    }

    .signature-space {
        height: 50px;
    }

    .signature-name {
        font-size: 10px;
        border-top: 1px solid #999;
        padding-top: 4px;
        margin-top: 4px;
    }

    .report-footer {
        text-align: center;
        font-size: 9px;
        color: #666;
        border-top: 1px solid #ccc;
        padding-top: 8px;
        margin-top: 10px;
    }

    @@media print {
        body { background: #fff !important; }
        .sidebar, .top-navbar, .no-print, .glass-card.h-100 { display: none !important; }
        .content { padding: 0 !important; margin: 0 !important; }
        .container-fluid { padding: 0 !important; }
        .col-12 { width: 100% !important; flex: 0 0 100% !important; max-width: 100% !important; }
        .report-container {
            border: none !important;
            padding: 15px 20px !important; page-break-after: avoid;
        }
        .report-summary-item { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .report-table thead th { background: #e0e0e0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .report-table tbody tr:nth-child(even) { background: #f8f8f8; }
        .report-footer { position: fixed; bottom: 0; left: 0; right: 0; }
        .page-number { content: counter(page); }
    }
</style>
@endpush
@endSection
