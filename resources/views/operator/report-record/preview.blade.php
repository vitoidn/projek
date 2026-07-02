@extends('layouts.dashboard')

@section('title', 'Report Record')
@section('page-title', 'Report Record')

@section('content')
<div class="glass-card no-print">
    <div class="d-flex justify-content-between align-items-center">
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
                    <td class="doc-info-value">QP-04002-0013-3</td>
                </tr>
                <tr>
                    <td class="doc-info-label">Revision</td>
                    <td class="doc-info-value">03</td>
                </tr>
                <tr>
                    <td class="doc-info-label">Retention</td>
                    <td class="doc-info-value">15 Tahun</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="report-title">
        PRODUCTION OPERATIONAL RECORD
    </div>

    <table class="report-info-table">
        <tr>
            <td class="report-info-label">Date</td>
            <td class="report-info-value">: {{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
            <td class="report-info-label">Process</td>
            <td class="report-info-value">: {{ $record->process_main }}{{ $record->process2_list ? ', ' . $record->process2_list : '' }}</td>
        </tr>
        <tr>
            <td class="report-info-label">Shift</td>
            <td class="report-info-value">: {{ $record->shift->name ?? '-' }}</td>
            <td class="report-info-label">NIK</td>
            <td class="report-info-value">: {{ $record->nik_list ?: '-' }}</td>
        </tr>
        <tr>
            <td class="report-info-label">Status</td>
            <td class="report-info-value">: {{ ucfirst($record->status) }}</td>
            <td class="report-info-label">Created By</td>
            <td class="report-info-value">: {{ $record->createdBy->name ?? '-' }}</td>
        </tr>
    </table>

    @if(is_array($record->niks) && count(array_filter($record->niks)))
    @php
        $roleLabels = [
            'manual_bending' => 'Manual Bending',
            'auto_bending' => 'Auto Bending',
            'bending' => 'Bending',
            'shape_check_jig' => 'Shape Check Jig',
            'drawing_inspection' => 'Drawing & Inspection',
            'drawing' => 'Drawing',
            'inspection' => 'Inspection',
        ];
    @endphp
    <div class="report-subtitle">Personnel Assignment</div>
    <table class="report-table personnel-table">
        <thead>
            <tr>
                <th>Role</th>
                <th>NIK</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->niks as $key => $val)
            @if($val && !empty($val['nik']))
            <tr>
                <td>{{ $roleLabels[$key] ?? ucwords(str_replace('_', ' ', $key)) }}</td>
                <td class="text-center">{{ $val['nik'] }}</td>
                <td>{{ $val['name'] ?? '-' }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    <table class="report-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-part">Part Code</th>
                <th class="col-lot">Lot Number</th>
                <th class="col-code">Code</th>
                <th class="col-activity">Activity</th>
                <th class="col-time">Start</th>
                <th class="col-time">End</th>
                <th class="col-min">Min</th>
                <th class="col-qty">QTY</th>
                <th class="col-qty">NG</th>
                <th class="col-qty">Hold</th>
                <th class="col-remark">Remark</th>
            </tr>
        </thead>
        <tbody>
            @forelse($record->bodies as $i => $body)
            <tr>
                <td class="col-no">{{ $i + 1 }}</td>
                <td class="col-part">{{ $body->part_code ?: '-' }}</td>
                <td class="col-lot">{{ $body->lot_id ?: '-' }}</td>
                <td class="col-code">{{ $body->code->code ?? '-' }}</td>
                <td class="col-activity">{{ $body->code->name ?? '' }}</td>
                <td class="col-time">{{ $body->start_time ? \Carbon\Carbon::parse($body->start_time)->format('H:i') : '-' }}</td>
                <td class="col-time">{{ $body->end_time ? \Carbon\Carbon::parse($body->end_time)->format('H:i') : '-' }}</td>
                <td class="col-min">{{ $body->duration_min }}</td>
                <td class="col-qty">{{ $body->qty }}</td>
                <td class="col-qty">{{ $body->ng }}</td>
                <td class="col-qty">{{ $body->hold }}</td>
                <td class="col-remark">{{ $body->remark ?: '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="text-center text-muted py-4">Belum ada data aktivitas</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-end fw-bold">Total</td>
                <td class="fw-bold">{{ $record->bodies->sum('duration_min') }}</td>
                <td class="fw-bold">{{ $record->bodies->sum('qty') }}</td>
                <td class="fw-bold">{{ $record->bodies->sum('ng') }}</td>
                <td class="fw-bold">{{ $record->bodies->sum('hold') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="report-signatures">
        <div class="signature-block">
            <div class="signature-label">Prepared by,</div>
            <div class="signature-space"></div>
            <div class="signature-name">{{ $record->createdBy->name ?? '______________________' }}</div>
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
        width: 90px;
        color: #333;
    }

    .report-info-value {
        width: 200px;
    }

    .report-subtitle {
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 6px;
        margin-top: 5px;
        color: #333;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .personnel-table {
        margin-bottom: 20px;
        width: 50%;
        min-width: 300px;
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
        padding: 4px 5px;
        vertical-align: middle;
    }

    .report-table tbody tr:nth-child(even) {
        background: #f8f8f8;
    }

    .report-table tfoot td {
        border: 1px solid #999;
        padding: 5px;
        background: #e8e8e8;
        font-size: 10px;
    }

    .col-no { width: 30px; text-align: center; }
    .col-part { width: 70px; text-align: center; }
    .col-lot { width: 100px; text-align: center; }
    .col-code { width: 40px; text-align: center; }
    .col-activity { width: 90px; }
    .col-time { width: 50px; text-align: center; }
    .col-min { width: 40px; text-align: center; }
    .col-qty { width: 40px; text-align: center; }
    .col-remark { min-width: 100px; }

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
        body { background: #fff; }
        .sidebar, .top-navbar, .no-print { display: none !important; }
        .content { padding: 0 !important; margin: 0 !important; }
        .container-fluid { padding: 0 !important; }
        .report-container {
            border: none !important;
            padding: 15px 20px !important;
        }
        .report-table tbody tr:nth-child(even) { background: #f8f8f8; }
        .report-table thead th { background: #e0e0e0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .report-table tfoot td { background: #e8e8e8 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .report-footer { position: fixed; bottom: 0; left: 0; right: 0; }
        .page-number { content: counter(page); }
    }
</style>
@endpush
@endSection
