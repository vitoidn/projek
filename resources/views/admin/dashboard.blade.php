@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('sidebar')
    <li class="active">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
    </li>
    <li>
        <a href="{{ route('admin.parts.index') }}"><i class="fas fa-database"></i> Master Data</a>
    </li>
    <li>
        <a href="#"><i class="fas fa-users"></i> Users & Roles</a>
    </li>
    <li>
        <a href="#"><i class="fas fa-history"></i> Audit Logs</a>
    </li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12 col-md-4">
        <div class="glass-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-light-primary me-3">
                    <i class="fas fa-database"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Master Data</h6>
                    <h3 class="fw-bold mb-0">24</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="glass-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-light-success me-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Active Users</h6>
                    <h3 class="fw-bold mb-0">12</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
