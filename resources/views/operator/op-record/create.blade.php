@extends('layouts.dashboard')

@section('title', 'Buat Operational Record Baru')
@section('page-title', 'Buat Operational Record Baru')

@section('content')
<div class="row g-4 justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="glass-card">
            <div class="border-bottom pb-3 mb-4">
                <h5 class="fw-bold text-dark mb-1">
                    <i class="ph ph-plus-circle text-primary me-2"></i> Header Operational Record
                </h5>
                <p class="small text-muted mb-0">Isi informasi header, lalu tambahkan detail aktivitas (Body) di halaman berikutnya.</p>
            </div>

            <form action="{{ route('operator.op-record.store') }}" method="POST">
                @csrf

                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label text-muted fw-bold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label text-muted fw-bold">Shift <span class="text-danger">*</span></label>
                        <select name="shift_id" class="form-select" required>
                            <option value="">-- Pilih Shift --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                    {{ $shift->name }} ({{ $shift->start_time }} - {{ $shift->end_time }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted fw-bold">Process (pilih salah satu) <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            @foreach(['Manual Bending' => 'manual_bending', 'Auto Bending' => 'auto_bending'] as $label => $key)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100 process-card" data-process="{{ $key }}">
                                    <div class="form-check mb-2">
                                        <input type="radio" name="process_main" value="{{ $label }}"
                                            class="form-check-input process-main-radio"
                                            id="pm_{{ $key }}"
                                            {{ old('process_main') == $label ? 'checked' : '' }}
                                            required>
                                        <label class="form-check-label fw-bold" for="pm_{{ $key }}">{{ $label }}</label>
                                    </div>
                                    <div class="nik-row ps-3 {{ old('process_main') == $label ? '' : 'd-none' }}">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="text" name="niks[{{ $key }}][nik]"
                                                    class="form-control form-control-sm" placeholder="NIK"
                                                    value="{{ old("niks.{$key}.nik") }}">
                                            </div>
                                            <div class="col-6">
                                                <input type="text" name="niks[{{ $key }}][name]"
                                                    class="form-control form-control-sm" placeholder="Nama"
                                                    value="{{ old("niks.{$key}.name") }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted fw-bold">Process 2 (bisa pilih >1)</label>
                        <div class="row g-2">
                            @foreach(['Shape Check Jig' => 'shape_check_jig', 'Drawing' => 'drawing', 'Inspection' => 'inspection'] as $label => $key)
                            <div class="col-md-4">
                                <div class="border rounded p-3 h-100 process-card" data-process="{{ $key }}">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" name="process_2[]" value="{{ $label }}"
                                            class="form-check-input process-2-checkbox"
                                            id="p2_{{ $key }}"
                                            {{ is_array(old('process_2')) && in_array($label, old('process_2')) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="p2_{{ $key }}">{{ $label }}</label>
                                    </div>
                                    <div class="nik-row ps-3 {{ is_array(old('process_2')) && in_array($label, old('process_2')) ? '' : 'd-none' }}">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="text" name="niks[{{ $key }}][nik]"
                                                    class="form-control form-control-sm" placeholder="NIK"
                                                    value="{{ old("niks.{$key}.nik") }}">
                                            </div>
                                            <div class="col-6">
                                                <input type="text" name="niks[{{ $key }}][name]"
                                                    class="form-control form-control-sm" placeholder="Nama"
                                                    value="{{ old("niks.{$key}.name") }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label text-muted fw-bold">Digital Signature (Prepare)</label>
                        <textarea name="prepare_signature" class="form-control" rows="2" placeholder="Optional: Tanda tangan digital">{{ old('prepare_signature') }}</textarea>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <a href="{{ route('operator.op-record.index') }}" class="btn btn-light fw-bold px-4 me-2">Batal</a>
                        <button type="submit" class="btn btn-primary fw-bold px-5">
                            <i class="ph ph-floppy-disk me-2"></i> Simpan Header & Lanjut
                        </button>
                    </div>
                </div>
            </form>
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
</script>
@endpush