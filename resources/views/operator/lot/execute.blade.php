@extends('layouts.dashboard')
@section('title', 'Eksekusi Lot')
@section('page-title')
    <a href="{{ route('operator.lot.index', $record->id) }}" class="btn btn-sm btn-light me-2"><i class="fas fa-arrow-left"></i></a> 
    Lot: {{ $lot->lot_number }} (Part: {{ $lot->part->part_code }})
@endsection

@section('content')
<div class="row g-4">
    <!-- Info Section -->
    <div class="col-12 col-md-4">
        <div class="glass-card mb-4">
            <h6 class="text-muted fw-bold mb-3">Informasi Target</h6>
            <table class="table table-sm table-borderless mb-0">
                <tr><td class="text-muted">Target Qty/Lot</td><td class="fw-bold text-primary">{{ $lot->qty_per_lot }} pcs</td></tr>
                <tr><td class="text-muted">Standard Time</td><td class="fw-bold text-info">{{ gmdate("H:i:s", $lot->standard_time_sec) }}</td></tr>
            </table>
        </div>
        @if($lot->status == 'Finished')
        <div class="glass-card bg-light-success border-success">
            <h6 class="text-success fw-bold mb-3">Hasil Produksi</h6>
            <table class="table table-sm table-borderless mb-0">
                <tr><td class="text-muted">Total Produksi</td><td class="fw-bold">{{ $lot->qty_production }} pcs</td></tr>
                <tr><td class="text-muted">Qty OK / NG</td><td class="fw-bold"><span class="text-success">{{ $lot->qty_ok }}</span> / <span class="text-danger">{{ $lot->qty_ng }}</span></td></tr>
                <tr><td class="text-muted">Status Waktu</td><td class="fw-bold">{{ $lot->production_status }}</td></tr>
                <tr><td class="text-muted">Working Time</td><td class="fw-bold">{{ gmdate("H:i:s", $lot->working_time_sec ?? 0) }}</td></tr>
            </table>
        </div>
        @endif
    </div>

    <!-- Timer & Controls -->
    <div class="col-12 col-md-8">
        <div class="glass-card h-100 d-flex flex-column justify-content-center align-items-center py-5">
            <h3 class="fw-bold mb-4" id="lotStatusText">
                @if($lot->status == 'Ready') Siap Dimulai
                @elseif($lot->status == 'Running') Sedang Berjalan
                @elseif($lot->status == 'Paused') Downtime Aktif
                @else Selesai
                @endif
            </h3>
            
            <div class="display-1 fw-bold text-dark mb-5 font-monospace" id="timerDisplay">
                @if($lot->status == 'Ready')
                    00:00:00
                @elseif($lot->status == 'Finished')
                    {{ gmdate("H:i:s", $lot->actual_time_sec) }}
                @else
                    --:--:--
                @endif
            </div>

            <div class="d-flex gap-3 justify-content-center w-100 px-5" id="controlsArea">
                @if($lot->status == 'Ready')
                    <button class="btn btn-success btn-lg px-5 py-3 fs-4 fw-bold w-100" id="btnStart" onclick="startLot()">START LOT</button>
                @elseif($lot->status == 'Running')
                    <button class="btn btn-warning btn-lg px-5 py-3 fs-4 fw-bold w-50" data-bs-toggle="modal" data-bs-target="#downtimeModal">DOWNTIME</button>
                    <button class="btn btn-primary btn-lg px-5 py-3 fs-4 fw-bold w-50" data-bs-toggle="modal" data-bs-target="#finishModal">LOT SELESAI</button>
                @elseif($lot->status == 'Paused')
                    <button class="btn btn-info btn-lg px-5 py-3 fs-4 fw-bold text-white w-100" onclick="endDowntime()">LANJUT (END DOWNTIME)</button>
                @elseif($lot->status == 'Finished')
                    <div class="alert alert-success w-100 fs-5 fw-bold text-center">Lot ini telah selesai dikerjakan.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Downtime Modal -->
<div class="modal fade" id="downtimeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title fw-bold text-warning">Mulai Downtime</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Alasan Downtime</label>
                    <select id="downtimeId" class="form-select">
                        <option value="">-- Pilih Alasan --</option>
                        @foreach($downtimesMaster as $dt)
                            <option value="{{ $dt->id }}">{{ $dt->name }} ({{ $dt->type }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning fw-bold" onclick="startDowntime()">Catat Downtime</button>
            </div>
        </div>
    </div>
</div>

<!-- Finish Modal -->
<div class="modal fade" id="finishModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title fw-bold text-primary">Selesaikan Lot</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">Qty OK (Good)</label>
                        <input type="number" id="qtyOk" class="form-control form-control-lg" value="{{ $lot->qty_per_lot }}" min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-danger">Qty NG (Not Good)</label>
                        <input type="number" id="qtyNg" class="form-control form-control-lg" value="0" min="0">
                    </div>
                    
                    <div class="col-12 mt-4">
                        <h6 class="fw-bold border-bottom pb-2">Catatan Defect (Opsional)</h6>
                        <div class="row g-2">
                            @foreach($defectsMaster as $defect)
                            <div class="col-md-4">
                                <label class="small text-muted">{{ $defect->name }}</label>
                                <input type="number" class="form-control form-control-sm defect-input" data-id="{{ $defect->id }}" value="0" min="0">
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kembali</button>
                <button type="button" class="btn btn-primary fw-bold px-4" onclick="finishLot()">Simpan & Selesai</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let lotId = {{ $lot->id }};
    let currentStatus = '{{ $lot->status }}';
    let startTime = '{{ $lot->start_time ? \Carbon\Carbon::parse($lot->start_time)->getTimestamp() : 0 }}';
    let totalDowntime = {{ $lot->total_downtime_sec ?? 0 }};
    let timerInterval = null;

    function updateTimer() {
        if(currentStatus === 'Running' && startTime > 0) {
            let now = Math.floor(Date.now() / 1000);
            let diff = now - startTime;
            let displayStr = new Date(diff * 1000).toISOString().substr(11, 8);
            $('#timerDisplay').text(displayStr);
        }
    }

    if(currentStatus === 'Running') {
        timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
    }

    function startLot() {
        $.post(`/operator/api/lot/${lotId}/start`, { _token: '{{ csrf_token() }}' }, function(res) {
            location.reload();
        }).fail(function(err) { alert('Error: ' + (err.responseJSON ? err.responseJSON.error : err.statusText)); });
    }

    function startDowntime() {
        let dtId = $('#downtimeId').val();
        if(!dtId) return alert('Pilih alasan downtime!');
        $.post(`/operator/api/lot/${lotId}/downtime/start`, { _token: '{{ csrf_token() }}', downtime_id: dtId }, function(res) {
            location.reload();
        }).fail(function(err) { alert('Error: ' + (err.responseJSON ? err.responseJSON.error : err.statusText)); });
    }

    function endDowntime() {
        $.post(`/operator/api/lot/${lotId}/downtime/end`, { _token: '{{ csrf_token() }}' }, function(res) {
            location.reload();
        }).fail(function(err) { alert('Error: ' + (err.responseJSON ? err.responseJSON.error : err.statusText)); });
    }

    function finishLot() {
        let ok = $('#qtyOk').val();
        let ng = $('#qtyNg').val();
        let defects = {};
        $('.defect-input').each(function() {
            let val = parseInt($(this).val());
            if(val > 0) defects[$(this).data('id')] = val;
        });

        $.post(`/operator/api/lot/${lotId}/finish`, { 
            _token: '{{ csrf_token() }}', 
            qty_ok: ok, 
            qty_ng: ng, 
            defects: defects 
        }, function(res) {
            location.reload();
        }).fail(function(err) { alert('Error: ' + (err.responseJSON ? err.responseJSON.error : err.statusText)); });
    }
</script>
@endpush
@endsection
