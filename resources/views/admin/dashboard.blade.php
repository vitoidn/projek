@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-2">
        <div class="glass-card text-center p-3">
            <i class="ph ph-users fa-2x text-primary mb-2"></i>
            <h3 class="fw-bold mb-0">{{ $totalUsers }}</h3>
            <small class="text-muted">Users</small>
        </div>
    </div>
    <div class="col-md-2">
        <div class="glass-card text-center p-3">
            <i class="ph ph-hash fa-2x text-success mb-2"></i>
            <h3 class="fw-bold mb-0">{{ $totalLotNumbers }}</h3>
            <small class="text-muted">LOT</small>
        </div>
    </div>
    <div class="col-md-2">
        <div class="glass-card text-center p-3">
            <i class="ph ph-code fa-2x text-info mb-2"></i>
            <h3 class="fw-bold mb-0">{{ $totalActivityCodes }}</h3>
            <small class="text-muted">Activity</small>
        </div>
    </div>
    <div class="col-md-2">
        <div class="glass-card text-center p-3">
            <i class="ph ph-gear-six fa-2x text-warning mb-2"></i>
            <h3 class="fw-bold mb-0">{{ $totalPartCodes }}</h3>
            <small class="text-muted">Part</small>
        </div>
    </div>
    <div class="col-md-2">
        <div class="glass-card text-center p-3">
            <i class="ph ph-buildings fa-2x text-secondary mb-2"></i>
            <h3 class="fw-bold mb-0">{{ $totalShifts }}</h3>
            <small class="text-muted">Shifts</small>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-lg-8">
        <div class="glass-card">
            <h5 class="fw-bold border-bottom pb-3 mb-3"><i class="ph ph-chart-line text-primary me-2"></i> Production Trend (7 Hari)</h5>
            <canvas id="productionTrendChart" height="100"></canvas>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="glass-card h-100">
            <h5 class="fw-bold border-bottom pb-3 mb-3"><i class="ph ph-chart-pie text-primary me-2"></i> Aktivitas (Top 5)</h5>
            <canvas id="activityChart" height="200"></canvas>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="glass-card text-center p-4">
            <small class="text-muted fw-bold">Today's Records</small>
            <h2 class="fw-bold mb-0 text-primary">{{ $recordsToday }}</h2>
            <small>Final: {{ $recordsFinalToday }}</small>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="glass-card text-center p-4">
            <small class="text-muted fw-bold">Today's QTY</small>
            <h2 class="fw-bold mb-0 text-success">{{ number_format($todaySummary->total_qty ?? 0) }}</h2>
            <small>NG: <span class="text-danger">{{ number_format($todaySummary->total_ng ?? 0) }}</span></small>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="glass-card text-center p-4">
            <small class="text-muted fw-bold">Today's Duration</small>
            <h2 class="fw-bold mb-0 text-info">{{ $todaySummary->total_duration_min ?? 0 }}</h2>
            <small>minutes</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-6">
        <div class="glass-card">
            <h5 class="fw-bold border-bottom pb-3 mb-3"><i class="ph ph-clock text-primary me-2"></i> Recent Records</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr><th>Date</th><th>Process</th><th>NIK</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentRecords as $rec)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($rec->date)->format('d M') }}</td>
                            <td>{{ $rec->process_main }}</td>
                            <td>{{ $rec->nik_list }}</td>
                            <td>{!! $rec->status == 'final' ? '<span class="badge bg-success">Final</span>' : '<span class="badge bg-warning text-dark">Draft</span>' !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="glass-card">
            <h5 class="fw-bold border-bottom pb-3 mb-3"><i class="ph ph-clock-counter-clockwise text-primary me-2"></i> Recent Audit Logs</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr><th>Table</th><th>Record</th><th>User</th><th>Time</th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentLogs as $log)
                        <tr>
                            <td><small>{{ $log->table_name }}</small></td>
                            <td>#{{ $log->record_id }}</td>
                            <td><small>{{ $log->changed_by }}</small></td>
                            <td><small>{{ $log->created_at->diffForHumans() }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endSection

@push('scripts')
<script>
    new Chart(document.getElementById('productionTrendChart'), {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [
                { label: 'QTY', data: @json($trendQty), borderColor: '#0d6efd', tension: 0.3, fill: false },
                { label: 'NG', data: @json($trendNg), borderColor: '#dc3545', tension: 0.3, fill: false }
            ]
        },
        options: { responsive: true, plugins: { legend: { display: true } } }
    });

    new Chart(document.getElementById('activityChart'), {
        type: 'doughnut',
        data: {
            labels: @json($activityLabels),
            datasets: [{ data: @json($activityDurations), backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545','#0dcaf0'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endpush