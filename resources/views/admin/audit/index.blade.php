@extends('layouts.dashboard')
@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs (Riwayat Perubahan Data)')

@section('content')
<div class="glass-card">
    <h5 class="fw-bold mb-4 text-dark border-bottom pb-2"><i class="fas fa-history text-primary me-2"></i> Sistem Audit Trail</h5>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Waktu (WIB)</th>
                    <th>Tabel Data</th>
                    <th>ID Record</th>
                    <th>Diubah Oleh</th>
                    <th>Alasan (Reason)</th>
                    <th>Perubahan Nilai (Lama -> Baru)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td class="text-nowrap">{{ $log->created_at->format('d M Y, H:i') }}</td>
                    <td class="fw-bold text-primary">{{ $log->table_name }}</td>
                    <td>#{{ $log->record_id }}</td>
                    <td class="fw-bold">{{ $log->changed_by }}</td>
                    <td class="text-muted fst-italic">{{ $log->reason }}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $log->id }}">
                            <i class="fas fa-eye me-1"></i> Lihat Detail
                        </button>
                    </td>
                </tr>

                <!-- Modal Detail -->
                <div class="modal fade" id="detailModal{{ $log->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Detail Perubahan Data (Audit ID: {{ $log->id }})</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-danger fw-bold border-bottom pb-2">Nilai Lama (Sebelum Diubah)</h6>
                                        <pre class="bg-light p-3 rounded text-wrap" style="max-height: 400px; overflow-y: auto;">
@php
$old = json_decode($log->old_value, true);
if($old) {
    foreach($old as $k => $v) {
        echo "<strong>{$k}</strong>: {$v}\n";
    }
} else {
    echo "N/A";
}
@endphp
                                        </pre>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success fw-bold border-bottom pb-2">Nilai Baru (Sesudah Diubah)</h6>
                                        <pre class="bg-light p-3 rounded text-wrap" style="max-height: 400px; overflow-y: auto;">
@php
$new = json_decode($log->new_value, true);
if($new) {
    foreach($new as $k => $v) {
        echo "<strong>{$k}</strong>: {$v}\n";
    }
} else {
    echo "N/A";
}
@endphp
                                        </pre>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @if($logs->isEmpty())
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat audit trail tercatat di sistem.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-end mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
