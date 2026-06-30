<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Monitoring Bending')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        :root {
            --primary-bg: #f4f7fe;
            --sidebar-bg: #111c44;
            --sidebar-color: #a0aec0;
            --accent-color: #4318ff;
        }
        body { background-color: var(--primary-bg); font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .wrapper { display: flex; width: 100%; align-items: stretch; min-height: 100vh; }
        
        .sidebar { min-width: 260px; max-width: 260px; background: var(--sidebar-bg); color: var(--sidebar-color); transition: all 0.3s; z-index: 999; }
        .sidebar .sidebar-header { padding: 30px 20px; text-align: center; font-weight: 700; font-size: 1.5rem; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .sidebar ul.components { padding: 20px 0; }
        .sidebar ul li a { padding: 15px 30px; font-size: 1rem; font-weight: 500; display: block; color: var(--sidebar-color); text-decoration: none; transition: 0.2s ease-in-out; }
        .sidebar ul li a:hover, .sidebar ul li.active > a { color: #fff; background: rgba(255,255,255,0.05); border-right: 4px solid var(--accent-color); }
        .sidebar ul li a i { margin-right: 12px; width: 20px; text-align: center; font-size: 1.1rem; }
        
        .content { width: 100%; padding: 20px 30px; transition: all 0.3s; }
        .top-navbar { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); padding: 15px 25px; border-radius: 16px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        
        .glass-card { background: #ffffff; border: none; border-radius: 20px; box-shadow: 0 5px 14px rgba(0,0,0,0.05); padding: 25px; transition: transform 0.2s; }
        .glass-card:hover { transform: translateY(-5px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        
        .stat-icon { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .bg-light-primary { background-color: #f4f7fe; color: #4318ff; }
        .bg-light-success { background-color: #e2fbd7; color: #34b53a; }
        .bg-light-danger { background-color: #ffe5d3; color: #ff3b30; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <nav class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-layer-group text-primary"></i> MonBend
            </div>
            <ul class="list-unstyled components">
                @if(auth()->check())
                    @if(auth()->user()->hasRole('admin'))
                        <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="#masterSubmenu" data-bs-toggle="collapse" class="dropdown-toggle"><i class="fas fa-database"></i> Master Data</a>
                            <ul class="collapse list-unstyled {{ request()->routeIs('admin.*') && !request()->routeIs('admin.dashboard') ? 'show' : '' }}" id="masterSubmenu">
                                <li><a href="{{ route('admin.parts.index') }}" class="ms-3"><i class="fas fa-cog"></i> Parts</a></li>
                                <li><a href="{{ route('admin.lines.index') }}" class="ms-3"><i class="fas fa-bars"></i> Lines</a></li>
                                <li><a href="{{ route('admin.shifts.index') }}" class="ms-3"><i class="fas fa-clock"></i> Shifts</a></li>
                                <li><a href="{{ route('admin.defects.index') }}"><i class="fas fa-exclamation-triangle"></i> Data Defect</a></li>
                                <li><a href="{{ route('admin.downtimes.index') }}"><i class="fas fa-stopwatch"></i> Data Downtime</a></li>
                            </ul>
                            
                            <h6 class="sidebar-heading px-3 mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Sistem</h6>
                            <li class="{{ request()->routeIs('admin.audit.index') ? 'active' : '' }}">
                                <a href="{{ route('admin.audit.index') }}"><i class="fas fa-history"></i> Audit Trail</a>
                            </li>
                        </li>
                    @elseif(auth()->user()->hasRole('supervisor'))
                        <li class="{{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('supervisor.dashboard') }}"><i class="fas fa-chart-line"></i> Monitoring</a>
                        </li>
                        <li class="{{ request()->routeIs('supervisor.planning.*') ? 'active' : '' }}">
                            <a href="{{ route('supervisor.planning.index') }}"><i class="fas fa-tasks"></i> Production Planning</a>
                        </li>
                        <li><a href="#"><i class="fas fa-file-alt"></i> Horenzo Reports</a></li>
                    @elseif(auth()->user()->hasRole('operator'))
                        <li class="{{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('operator.dashboard') }}"><i class="fas fa-clipboard-list"></i> O/R Header</a>
                        </li>
                        <li><a href="#"><i class="fas fa-play-circle"></i> Eksekusi Lot</a></li>
                        <li><a href="#"><i class="fas fa-history"></i> Riwayat Hari Ini</a></li>
                    @endif
                @endif
            </ul>
        </nav>

        <div class="content">
            <div class="top-navbar">
                <div>
                    <h4 class="mb-0 fw-bold text-dark">@yield('page-title', 'Dashboard')</h4>
                    <small class="text-muted">Welcome back, {{ Auth::user()->name ?? 'User' }}</small>
                </div>
                <div class="dropdown">
                    <button class="btn btn-white rounded-pill px-3 shadow-sm fw-bold text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle text-primary fs-5 align-middle me-1"></i> {{ Auth::user()->name ?? 'User' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="border-radius: 12px;">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger fw-500 py-2"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="container-fluid p-0">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
