@extends('layouts.dashboard')

@section('title', 'My Operational Records')
@section('page-title', 'My Operational Records')

@push('styles')
<style>
    .record-entry {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        transition: box-shadow 0.15s ease;
        margin-bottom: 16px;
        scroll-margin-top: 20px;
    }
    .record-entry:hover {
        box-shadow: 0 1px 6px rgba(0,0,0,0.04);
    }
    .record-entry-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        cursor: pointer;
        user-select: none;
        background: #fafbfc;
        transition: background 0.15s ease;
        border-left: 4px solid #e2e8f0;
    }
    .record-entry-header:hover {
        background: #f1f5f9;
    }
    .record-entry-header.is-manual {
        border-left-color: #3b82f6;
    }
    .record-entry-header.is-auto {
        border-left-color: #8b5cf6;
    }
    .record-entry-header .toggle-icon {
        transition: transform 0.2s ease;
        font-size: 1rem;
        color: #94a3b8;
    }
    .record-entry-header .toggle-icon.collapsed {
        transform: rotate(-90deg);
    }
    .record-entry-header .header-info {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .record-entry-header .header-date {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.9rem;
        min-width: 90px;
    }
    .record-entry-header .header-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: auto;
    }
    .record-summary {
        display: flex;
        gap: 16px;
        padding: 10px 16px;
        background: #fff;
        border-top: 1px solid #f1f5f9;
        font-size: 0.8rem;
        color: #475569;
    }
    .record-summary span {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .record-summary i {
        font-size: 0.9rem;
        color: #94a3b8;
    }
    .body-section {
        border-top: 1px solid #e2e8f0;
    }
    .body-row {
        transition: background-color 0.2s ease;
    }
    .body-row:hover {
        background-color: #fafbfc;
    }
    .body-row.row-saved {
        animation: rowFlash 0.6s ease;
    }
    @keyframes rowFlash {
        0% { background-color: #d1fae5; }
        100% { background-color: transparent; }
    }
    .body-row td { vertical-align: middle; }
    .duration-display {
        font-weight: 600;
        color: #4f46e5;
        font-variant-numeric: tabular-nums;
    }
    .body-table-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .body-table-wrap .table {
        margin-bottom: 0;
        border: none;
    }
    .body-table-wrap .table thead th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        font-size: 0.7rem;
        padding: 10px 8px;
        white-space: nowrap;
    }
    .body-table-wrap .table tbody td {
        padding: 6px 6px;
        border-bottom: 1px solid #f1f5f9;
    }
    .body-table-wrap .table tfoot td {
        padding: 10px 8px;
        background: #f8fafc;
        border-top: 2px solid #e2e8f0;
        font-size: 0.85rem;
    }
    .body-table-wrap .form-control-sm,
    .body-table-wrap .form-select-sm {
        border-radius: 6px;
        padding: 6px 8px;
        font-size: 0.8rem;
        border: 1.5px solid #e2e8f0;
        min-height: 34px;
    }
    .body-table-wrap .form-control-sm:focus,
    .body-table-wrap .form-select-sm:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 2px rgba(79,70,229,0.08);
    }
    .row-actions {
        display: flex;
        gap: 4px;
        justify-content: center;
    }
    .row-actions .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1.5px solid transparent;
        transition: all 0.15s ease;
        font-size: 1rem;
    }
    .row-actions .btn-delete {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }
    .row-actions .btn-delete:hover {
        background: #b91c1c;
        border-color: #b91c1c;
        color: #fff;
    }
    .row-number {
        width: 32px;
        text-align: center;
        color: #94a3b8;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0 4px !important;
    }
    .row-number::before {
        counter-increment: body-row;
        content: counter(body-row);
    }
    .body-table-tbody {
        counter-reset: body-row;
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-muted small">Klik header record untuk buka/tutup tabel aktivitas</div>
            <a href="{{ route('operator.op-record.create') }}" class="btn btn-primary fw-bold">
                <i class="ph ph-plus-circle me-2"></i> Buat Baru
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @forelse($records as $rec)
        <div class="record-entry" id="record-{{ $rec->id }}">
            <div class="record-entry-header {{ str_contains($rec->process_main, 'Auto') ? 'is-auto' : 'is-manual' }}"
                 data-target="body-section-{{ $rec->id }}">
                <div class="header-info">
                    <span class="header-date">{{ \Carbon\Carbon::parse($rec->date)->format('d M Y') }}</span>
                    <span class="badge bg-primary">{{ $rec->process_main }}</span>
                    <span class="text-muted small">{{ $rec->shift->name ?? '-' }}</span>
                    <small class="text-muted nik-list-inline">{{ $rec->nik_list }}</small>
                </div>
                <div class="header-actions">
                    @if($rec->status == 'draft')
                        <span class="badge bg-warning text-dark">Draft</span>
                    @else
                        <span class="badge bg-success">Final</span>
                    @endif
                    <a href="{{ route('operator.op-record.show', $rec->id) }}" class="btn btn-sm btn-info text-white" title="Lihat" onclick="event.stopPropagation()"><i class="ph ph-eye"></i></a>
                    @if($rec->status == 'draft')
                        <a href="{{ route('operator.op-record.edit', $rec->id) }}" class="btn btn-sm btn-warning text-white" title="Edit Header" onclick="event.stopPropagation()"><i class="ph ph-pencil"></i></a>
                    @endif
                    <a href="{{ route('operator.report-record.preview', $rec->id) }}" class="btn btn-sm btn-secondary text-white" title="Report Record" onclick="event.stopPropagation()"><i class="ph ph-file-arrow-down"></i></a>
                    <i class="ph ph-caret-down toggle-icon {{ $rec->status == 'final' ? 'collapsed' : '' }}"></i>
                </div>
            </div>

            @php
                $totalMin = $rec->bodies->sum('duration_min');
                $totalQty = $rec->bodies->sum('qty');
                $bodyCount = $rec->bodies->count();
            @endphp
            @if($bodyCount > 0)
            <div class="record-summary">
                <span><i class="ph ph-list-dashes"></i> {{ $bodyCount }} baris</span>
                <span><i class="ph ph-clock"></i> {{ intdiv($totalMin, 60) }}j {{ $totalMin % 60 }}m</span>
                @if($totalQty > 0)<span><i class="ph ph-cube"></i> {{ number_format($totalQty) }} qty</span>@endif
            </div>
            @endif

            <div class="body-section {{ $rec->status == 'final' ? 'd-none' : '' }}" id="body-section-{{ $rec->id }}">
                <div class="p-3">
                    <div class="body-table-wrap">
                        <div class="table-responsive">
                            <table class="table align-middle body-table" data-header-id="{{ $rec->id }}">
                                <thead class="table-light">
                                    <tr>
                                        <th class="row-number" style="width:36px">#</th>
                                        <th>Part Code</th>
                                        <th>LOT</th>
                                        <th>Activity</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th style="width:50px">Min</th>
                                        <th style="width:70px">QTY</th>
                                        <th style="width:70px">NG</th>
                                        <th style="width:70px">Hold</th>
                                        <th>Remark</th>
                                        <th style="width:50px" class="text-center">Del</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table-tbody">
                                    @foreach($rec->bodies as $body)
                                    <tr class="body-row" data-id="{{ $body->id }}">
                                        <td class="row-number"></td>
                                        <td><input type="text" class="form-control form-control-sm part-code" value="{{ $body->part_code }}" placeholder="Part code"></td>
                                        <td><input type="text" class="form-control form-control-sm lot-id" value="{{ $body->lot_id }}" placeholder="LOT"></td>
                                        <td>
                                            <select class="form-select form-select-sm code-id" required>
                                                <option value="">Pilih</option>
                                                @foreach($activityCodes as $ac)
                                                    <option value="{{ $ac->id }}" {{ $body->code_id == $ac->id ? 'selected' : '' }}>{{ $ac->code }} - {{ $ac->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="time" class="form-control form-control-sm start-time" value="{{ $body->start_time }}" required></td>
                                        <td><input type="time" class="form-control form-control-sm end-time" value="{{ $body->end_time }}" required></td>
                                        <td class="duration-display text-center">{{ $body->duration_min }}</td>
                                        <td><input type="number" class="form-control form-control-sm qty" value="{{ $body->qty }}" min="0" placeholder="0"></td>
                                        <td><input type="number" class="form-control form-control-sm ng" value="{{ $body->ng }}" min="0" placeholder="0"></td>
                                        <td><input type="number" class="form-control form-control-sm hold" value="{{ $body->hold }}" min="0" placeholder="0"></td>
                                        <td><input type="text" class="form-control form-control-sm remark" value="{{ $body->remark }}" placeholder="Remark"></td>
                                        <td>
                                            <div class="row-actions">
                                                <button type="button" class="btn-icon btn-delete remove-row" title="Hapus"><i class="ph ph-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="totals-row">
                                        <td></td>
                                        <td colspan="4" class="text-end fw-semibold text-secondary">Total:</td>
                                        <td></td>
                                        <td class="text-center fw-bold duration-total">0</td>
                                        <td class="fw-bold qty-total">0</td>
                                        <td class="fw-bold text-danger ng-total">0</td>
                                        <td class="fw-bold text-warning hold-total">0</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="mt-2 d-flex gap-2">
                        <button type="button" class="btn btn-success btn-sm fw-bold add-row" data-header-id="{{ $rec->id }}">
                            <i class="ph ph-plus me-1"></i> Tambah Baris
                        </button>
                        @if($totalMin > 0)
                        <span class="btn btn-sm text-muted" style="cursor:default;border:1px solid transparent;">
                            <i class="ph ph-clock"></i> {{ intdiv($totalMin, 60) }}j {{ $totalMin % 60 }}m total
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="glass-card p-5 text-center">
            <i class="ph ph-clipboard-text text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Belum ada record. Buat baru untuk memulai.</p>
        </div>
        @endforelse

        <div class="mt-3">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        const csrf = '{{ csrf_token() }}';

        // Toggle body section on header click
        $('.record-entry-header').on('click', function () {
            const targetId = $(this).data('target');
            const $section = $('#' + targetId);
            const $icon = $(this).find('.toggle-icon');
            $section.toggleClass('d-none');
            $icon.toggleClass('collapsed');
        });

        function calcDuration(start, end) {
            if (!start || !end) return 0;
            const [sh, sm] = start.split(':').map(Number);
            const [eh, em] = end.split(':').map(Number);
            const diff = (eh * 60 + em) - (sh * 60 + sm);
            return diff > 0 ? diff : 0;
        }

        function updateTotals($table) {
            let totalDur = 0, totalQty = 0, totalNg = 0, totalHold = 0;
            $table.find('.body-table-tbody tr').each(function () {
                totalDur += parseInt($(this).find('.duration-display').text()) || 0;
                totalQty += parseInt($(this).find('.qty').val()) || 0;
                totalNg += parseInt($(this).find('.ng').val()) || 0;
                totalHold += parseInt($(this).find('.hold').val()) || 0;
            });
            $table.find('.duration-total').text(totalDur);
            $table.find('.qty-total').text(totalQty.toLocaleString());
            $table.find('.ng-total').text(totalNg.toLocaleString());
            $table.find('.hold-total').text(totalHold.toLocaleString());
        }

        function getRowData($row) {
            return {
                part_code: $row.find('.part-code').val() || '',
                lot_id: $row.find('.lot-id').val() || '',
                code_id: $row.find('.code-id').val(),
                start_time: $row.find('.start-time').val(),
                end_time: $row.find('.end-time').val(),
                qty: $row.find('.qty').val() || 0,
                ng: $row.find('.ng').val() || 0,
                hold: $row.find('.hold').val() || 0,
                remark: $row.find('.remark').val() || '',
            };
        }

        function flashRow($row) {
            $row.addClass('row-saved');
            setTimeout(() => $row.removeClass('row-saved'), 600);
        }

        function autoSaveRow($row, headerId) {
            if ($row.data('creating')) return;
            if ($row.data('saving')) return;
            $row.data('saving', true);

            const id = $row.data('id');
            const data = getRowData($row);

            if (!data.code_id || !data.start_time || !data.end_time) {
                $row.data('saving', false);
                return;
            }

            const isNew = !id;
            const url = isNew
                ? `/operator/operational-records/${headerId}/bodies`
                : `/operator/operational-records/${headerId}/bodies/${id}`;
            const method = isNew ? 'POST' : 'PUT';

            $.ajax({
                url, method,
                data: { ...data, _token: csrf },
                success: function (res) {
                    const saved = res.data || res;
                    if (isNew && saved.id) {
                        $row.attr('data-id', saved.id);
                    }
                    flashRow($row);
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.error
                        || xhr.responseJSON?.message
                        || (xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors).flat().join(', ') : null)
                        || 'Error saving row';
                    alert(msg);
                },
                complete: function () {
                    $row.data('saving', false);
                }
            });
        }

        const codeOpts = `<option value="">Pilih</option>@foreach($activityCodes as $ac)<option value="{{ $ac->id }}">{{ $ac->code }} - {{ $ac->name }}</option>@endforeach`;

        function createNewRow(data) {
            const dur = data ? calcDuration(data.start_time, data.end_time) : 0;
            const startVal = data?.start_time || '';
            const endVal = data?.end_time || '';

            return `<tr class="body-row" data-id="${data?.id || ''}">
                <td class="row-number"></td>
                <td><input type="text" class="form-control form-control-sm part-code" value="${data?.part_code || ''}" placeholder="Part code"></td>
                <td><input type="text" class="form-control form-control-sm lot-id" value="${data?.lot_id || ''}" placeholder="LOT"></td>
                <td><select class="form-select form-select-sm code-id" required>${codeOpts}</select></td>
                <td><input type="time" class="form-control form-control-sm start-time" value="${startVal}" required></td>
                <td><input type="time" class="form-control form-control-sm end-time" value="${endVal}" required></td>
                <td class="duration-display text-center">${dur}</td>
                <td><input type="number" class="form-control form-control-sm qty" value="${data?.qty || 0}" min="0" placeholder="0"></td>
                <td><input type="number" class="form-control form-control-sm ng" value="${data?.ng || 0}" min="0" placeholder="0"></td>
                <td><input type="number" class="form-control form-control-sm hold" value="${data?.hold || 0}" min="0" placeholder="0"></td>
                <td><input type="text" class="form-control form-control-sm remark" value="${data?.remark || ''}" placeholder="Remark"></td>
                <td>
                    <div class="row-actions">
                        <button type="button" class="btn-icon btn-delete remove-row" title="Hapus"><i class="ph ph-trash"></i></button>
                    </div>
                </td>
            </tr>`;
        }

        function bindRowEvents($row, headerId) {
            const $table = $row.closest('.body-table');

            $row.find('.start-time, .end-time').on('change', function () {
                const start = $row.find('.start-time').val();
                const end = $row.find('.end-time').val();
                $row.find('.duration-display').text(calcDuration(start, end));
                updateTotals($table);
            });

            $row.find('.qty, .ng, .hold').on('input', function () {
                updateTotals($table);
            });

            $row.find('.remove-row').off('click').on('click', function () {
                const $btn = $(this);
                showConfirm({
                    title: 'Hapus Baris',
                    message: 'Hapus baris ini? Data tidak bisa dikembalikan.',
                    confirmText: 'Ya, Hapus',
                    danger: true,
                    icon: 'trash',
                }).then(function (ok) {
                    if (!ok) return;
                    const id = $row.data('id');

                    if (id) {
                        $btn.prop('disabled', true).html('<i class="ph ph-spinner-gap"></i>');
                        $.ajax({
                            url: `/operator/operational-records/${headerId}/bodies/${id}`,
                            method: 'DELETE',
                            data: { _token: csrf },
                            success: function () {
                                $row.fadeOut(200, function () { $(this).remove(); updateTotals($table); });
                            },
                            error: function (xhr) {
                                $btn.prop('disabled', false).html('<i class="ph ph-trash"></i>');
                                alert(xhr.responseJSON?.error || 'Error deleting row');
                            }
                        });
                    } else {
                        $row.fadeOut(200, function () { $(this).remove(); updateTotals($table); });
                    }
                });
            });

            let debounceTimer = null;
            $row.find('input, select').on('change input', function () {
                if (debounceTimer) clearTimeout(debounceTimer);
                const immediate = $(this).is('.start-time, .end-time, .code-id');
                debounceTimer = setTimeout(() => autoSaveRow($row, headerId), immediate ? 0 : 600);
            });
        }

        function initTable($table) {
            const headerId = $table.data('header-id');
            const $tbody = $table.find('.body-table-tbody');

            $tbody.find('tr').each(function () {
                bindRowEvents($(this), headerId);
            });

            updateTotals($table);
        }

        $('.body-table').each(function () {
            initTable($(this));
        });

        $('.add-row').on('click', function () {
            const headerId = $(this).data('header-id');
            const $table = $(this).closest('.body-section').find('.body-table');
            const $tbody = $table.find('.body-table-tbody');
            const $newRow = $(createNewRow({}));
            $newRow.data('creating', true);
            $tbody.append($newRow);
            bindRowEvents($newRow, headerId);
            updateTotals($table);

            $.ajax({
                url: `/operator/operational-records/${headerId}/bodies`,
                method: 'POST',
                data: { _token: csrf },
                success: function (res) {
                    const saved = res.data || res;
                    if (saved && saved.id) {
                        $newRow.attr('data-id', saved.id);
                    }
                },
                error: function (xhr) {
                    $newRow.remove();
                    updateTotals($table);
                    const msg = xhr.responseJSON?.error
                        || xhr.responseJSON?.message
                        || (xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors).flat().join(', ') : null)
                        || 'Gagal membuat baris baru.';
                    alert(msg);
                },
                complete: function () {
                    $newRow.data('creating', false);
                }
            });
        });
    });
</script>
@endpush
