@extends('layouts.dashboard')

@section('title', 'Edit Operational Record')
@section('page-title')
    <a href="{{ route('operator.op-record.index') }}" class="btn btn-sm btn-light me-2"><i class="ph ph-arrow-left"></i></a>
    Edit Record: {{ \Carbon\Carbon::parse($record->date)->format('d M Y') }} - {{ $record->process_main }}
@endsection

@push('styles')
<style>
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
    .body-row.row-unsaved {
        border-left: 3px solid #f59e0b;
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
    #bodyTableBody {
        counter-reset: body-row;
    }
    .input-table-cell {
        min-width: 0;
    }
</style>
@endpush

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="glass-card mb-4">
            <h5 class="fw-bold border-bottom pb-3 mb-3">
                <i class="ph ph-info text-primary me-2"></i> Header Info
            </h5>

            <form id="headerForm" action="{{ route('operator.op-record.update', $record->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold small">Date</label>
                        <input type="date" name="date" class="form-control form-control-sm" value="{{ $record->date->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold small">Shift</label>
                        <select name="shift_id" class="form-select form-select-sm" required>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ $record->shift_id == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted fw-bold small">Signature</label>
                        <textarea name="prepare_signature" class="form-control form-control-sm" rows="2">{{ $record->prepare_signature }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted fw-bold small">Process (pilih salah satu) <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            @foreach(['Shape Check Jig' => 'shape_check_jig', 'Drawing & Inspection' => 'drawing_inspection'] as $label => $key)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100 process-card" data-process="{{ $key }}">
                                    <div class="form-check mb-2">
                                        <input type="radio" name="process_main" value="{{ $label }}"
                                            class="form-check-input process-main-radio"
                                            id="epm_{{ $key }}"
                                            {{ $record->process_main == $label ? 'checked' : '' }}
                                            required>
                                        <label class="form-check-label fw-bold" for="epm_{{ $key }}">{{ $label }}</label>
                                    </div>
                                    <div class="nik-row ps-3 {{ $record->process_main == $label ? '' : 'd-none' }}">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="text" name="niks[{{ $key }}][nik]"
                                                    class="form-control form-control-sm" placeholder="NIK"
                                                    value="{{ $record->niks[$key]['nik'] ?? '' }}">
                                            </div>
                                            <div class="col-6">
                                                <input type="text" name="niks[{{ $key }}][name]"
                                                    class="form-control form-control-sm" placeholder="Nama"
                                                    value="{{ $record->niks[$key]['name'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted fw-bold small">Process 2 (bisa pilih >1)</label>
                        <div class="row g-2">
                            @foreach(['Shape Check Jig' => 'shape_check_jig', 'Drawing' => 'drawing', 'Inspection' => 'inspection'] as $label => $key)
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100 process-card" data-process="{{ $key }}">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="process_2[]" value="{{ $label }}"
                                            class="form-check-input process-2-checkbox"
                                            id="ep2_{{ $key }}"
                                            {{ is_array($record->process_2) && in_array($label, $record->process_2) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="ep2_{{ $key }}">{{ $label }}</label>
                                    </div>
                                    <div class="nik-row ps-3 {{ is_array($record->process_2) && in_array($label, $record->process_2) ? '' : 'd-none' }}">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="text" name="niks[{{ $key }}][nik]"
                                                    class="form-control form-control-sm" placeholder="NIK"
                                                    value="{{ $record->niks[$key]['nik'] ?? '' }}">
                                            </div>
                                            <div class="col-6">
                                                <input type="text" name="niks[{{ $key }}][name]"
                                                    class="form-control form-control-sm" placeholder="Nama"
                                                    value="{{ $record->niks[$key]['name'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="ph ph-floppy-disk me-1"></i> Update Header</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <h5 class="fw-bold mb-0"><i class="ph ph-table text-primary me-2"></i> Detail Aktivitas (Body)</h5>
                <button type="button" class="btn btn-success btn-sm fw-bold" id="addRowBtn">
                    <i class="ph ph-plus me-1"></i> Tambah Baris
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="body-table-wrap">
                <div class="table-responsive">
                    <table class="table align-middle" id="bodyTable">
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
                                <th style="width:90px" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bodyTableBody">
                            @foreach($record->bodies as $body)
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
                            <tr>
                                <td></td>
                                <td colspan="4" class="text-end fw-semibold text-secondary">Total:</td>
                                <td></td>
                                <td class="text-center fw-bold" id="totalDuration">{{ $record->bodies->sum('duration_min') }}</td>
                                <td class="fw-bold" id="totalQty">{{ number_format($record->bodies->sum('qty')) }}</td>
                                <td class="fw-bold text-danger" id="totalNg">{{ number_format($record->bodies->sum('ng')) }}</td>
                                <td class="fw-bold text-warning" id="totalHold">{{ number_format($record->bodies->sum('hold')) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                <a href="{{ route('operator.op-record.show', $record->id) }}" class="btn btn-light fw-bold px-3"><i class="ph ph-arrow-left me-1"></i> Kembali</a>
                <form action="{{ route('operator.op-record.submit', $record->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success fw-bold px-4"
                        data-confirm="Submit sebagai Final? Semua baris akan terkunci dan tidak bisa diedit lagi."
                        data-confirm-yes="Ya, Finalkan">
                        <i class="ph ph-check-circle me-2"></i> Submit Final
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endSection

@push('scripts')
<script>
    document.querySelectorAll('.process-main-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.process-card[data-process]').forEach(card => {
                const nikRow = card.querySelector('.nik-row');
                if (nikRow) nikRow.classList.add('d-none');
            });
            const parent = this.closest('.process-card');
            if (parent) {
                const nikRow = parent.querySelector('.nik-row');
                if (nikRow) nikRow.classList.remove('d-none');
            }
        });
    });

    document.querySelectorAll('.process-2-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const parent = this.closest('.process-card');
            if (parent) {
                const nikRow = parent.querySelector('.nik-row');
                if (nikRow) nikRow.classList.toggle('d-none', !this.checked);
            }
        });
    });

    $(document).ready(function () {
        const headerId = {{ $record->id }};
        const csrf = '{{ csrf_token() }}';

        function calcDuration(start, end) {
            if (!start || !end) return 0;
            const [sh, sm] = start.split(':').map(Number);
            const [eh, em] = end.split(':').map(Number);
            const diff = (eh * 60 + em) - (sh * 60 + sm);
            return diff > 0 ? diff : 0;
        }

        function updateTotals() {
            let totalDur = 0, totalQty = 0, totalNg = 0, totalHold = 0;
            $('#bodyTableBody tr').each(function () {
                totalDur += parseInt($(this).find('.duration-display').text()) || 0;
                totalQty += parseInt($(this).find('.qty').val()) || 0;
                totalNg += parseInt($(this).find('.ng').val()) || 0;
                totalHold += parseInt($(this).find('.hold').val()) || 0;
            });
            $('#totalDuration').text(totalDur);
            $('#totalQty').text(totalQty.toLocaleString());
            $('#totalNg').text(totalNg.toLocaleString());
            $('#totalHold').text(totalHold.toLocaleString());
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

        function autoSaveRow($row) {
            if ($row.data('creating')) return;
            if ($row.data('saving')) return;
            $row.data('saving', true);

            const id = $row.data('id');
            const data = getRowData($row);

            if (!data.code_id || !data.start_time || !data.end_time) {
                $row.data('saving', false);
                return;
            }

            $.ajax({
                url: `/operator/operational-records/${headerId}/bodies/${id}`,
                method: 'PUT',
                data: { ...data, _token: csrf },
                success: function (res) {
                    flashRow($row);
                    $row.removeClass('row-unsaved');
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

        function bindRowEvents($row) {
            $row.find('.start-time, .end-time').on('change', function () {
                const start = $row.find('.start-time').val();
                const end = $row.find('.end-time').val();
                $row.find('.duration-display').text(calcDuration(start, end));
                updateTotals();
            });

            $row.find('.qty, .ng, .hold').on('input', function () {
                updateTotals();
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
                                $row.fadeOut(200, function () { $(this).remove(); updateTotals(); });
                            },
                            error: function (xhr) {
                                $btn.prop('disabled', false).html('<i class="ph ph-trash"></i>');
                                alert(xhr.responseJSON?.error || 'Error deleting row');
                            }
                        });
                    } else {
                        $row.fadeOut(200, function () { $(this).remove(); updateTotals(); });
                    }
                });
            });

            let debounceTimer = null;
            $row.find('input, select').on('change input', function () {
                if (debounceTimer) clearTimeout(debounceTimer);
                const immediate = $(this).is('.start-time, .end-time, .code-id');
                debounceTimer = setTimeout(() => autoSaveRow($row), immediate ? 0 : 600);
            });
        }

        function createNewRow(data) {
            const codeOpts = `<option value="">Pilih</option>@foreach($activityCodes as $ac)<option value="{{ $ac->id }}">{{ $ac->code }} - {{ $ac->name }}</option>@endforeach`;
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

        $('#addRowBtn').on('click', function () {
            const $newRow = $(createNewRow({}));
            $newRow.data('creating', true);
            $('#bodyTableBody').append($newRow);
            bindRowEvents($newRow);

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
                    updateTotals();
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

        $('#bodyTableBody tr').each(function () {
            bindRowEvents($(this));
        });
    });
</script>
@endpush