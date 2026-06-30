@extends('layouts.dashboard')
@section('title', 'Dashboard Monitoring')
@section('page-title')
    Monitoring Produksi Hari Ini ({{ \Carbon\Carbon::parse($today)->format('d M Y') }})
@endsection

@section('content')
<!-- Ringkasan Global -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="glass-card text-center p-4 bg-primary text-white h-100 shadow-sm border-0">
            <h6 class="fw-bold mb-3 opacity-75">Total Target Produksi</h6>
            <h2 class="display-5 fw-bold mb-0">{{ number_format($totalTargetDay) }}</h2>
            <span class="small">Pcs</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card text-center p-4 bg-success text-white h-100 shadow-sm border-0">
            <h6 class="fw-bold mb-3 opacity-75">Aktual Produksi</h6>
            <h2 class="display-5 fw-bold mb-0">{{ number_format($totalActualDay) }}</h2>
            <span class="small">Pcs</span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass-card text-center p-4 text-white h-100 shadow-sm border-0 {{ $dayAchievement >= 100 ? 'bg-info' : 'bg-warning text-dark' }}">
            <h6 class="fw-bold mb-3 {{ $dayAchievement >= 100 ? 'opacity-75' : '' }}">Achievement</h6>
            <h2 class="display-5 fw-bold mb-0">{{ $dayAchievement }}%</h2>
            <span class="small">Berdasarkan plan berjalan</span>
        </div>
    </div>
</div>

<!-- Monitoring Per Line -->
<div class="glass-card p-4">
    <h5 class="fw-bold mb-4 text-dark border-bottom pb-2"><i class="fas fa-industry text-primary me-2"></i> Status Per Line</h5>
    <div class="row g-4">
        @foreach($lineData as $data)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-light border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">{{ $data['name'] }}</h5>
                    @if($data['running_lots'] > 0)
                        <span class="badge bg-success pulse-animation"><i class="fas fa-play-circle me-1"></i> {{ $data['running_lots'] }} Lot Running</span>
                    @else
                        <span class="badge bg-secondary">Standby</span>
                    @endif
                </div>
                <div class="card-body p-4 bg-white">
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Target: <strong>{{ number_format($data['target']) }}</strong></span>
                        <span>Actual: <strong>{{ number_format($data['actual']) }}</strong></span>
                    </div>
                    
                    <div class="progress" style="height: 25px; border-radius: 10px;">
                        @php
                            $bgClass = 'bg-primary';
                            if($data['achievement'] >= 100) $bgClass = 'bg-success';
                            elseif($data['achievement'] < 50 && $data['target'] > 0) $bgClass = 'bg-danger';
                        @endphp
                        <div class="progress-bar {{ $bgClass }} progress-bar-striped {{ $data['running_lots'] > 0 ? 'progress-bar-animated' : '' }}" 
                             role="progressbar" 
                             style="width: {{ min($data['achievement'], 100) }}%" 
                             aria-valuenow="{{ $data['achievement'] }}" aria-valuemin="0" aria-valuemax="100">
                             {{ $data['achievement'] }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
.pulse-animation {
    animation: pulse 1.5s infinite;
}
@keyframes pulse {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.8; transform: scale(1.05); box-shadow: 0 0 8px rgba(40,167,69,0.5); }
    100% { opacity: 1; transform: scale(1); }
}
</style>
@endsection
