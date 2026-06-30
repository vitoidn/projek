<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Monitoring Bending')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/fill/style.css"/>
    <link href="{{ asset('css/dashboard-modern.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        <nav class="sidebar">
            <div class="sidebar-header">
                <i class="ph ph-stack text-primary"></i> MonBend
            </div>
            <ul class="list-unstyled components">
                @if(auth()->check())
                    @if(auth()->user()->hasRole('supervisor'))
                        <li class="{{ request()->routeIs('admin.dashboard') || request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin.dashboard') }}"><i class="ph ph-chart-line"></i> Monitoring</a>
                        </li>
                        <li class="{{ request()->routeIs('supervisor.planning.*') || request()->routeIs('admin.planning.*') ? 'active' : '' }}">
                            <a href="{{ route('supervisor.planning.index') }}"><i class="ph ph-list-checks"></i> Production Planning</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.op-record.*') || request()->routeIs('supervisor.op-record.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.op-record.index') }}"><i class="ph ph-clipboard-text"></i> Operational Records</a>
                        </li>
                        <li class="{{ request()->routeIs('admin.horenzo.*') || request()->routeIs('supervisor.horenzo.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.horenzo.index') }}"><i class="ph ph-file-text"></i> Horenzo Reports</a>
                        </li>
                        <li>
                            <a href="#masterSubmenu" data-bs-toggle="collapse" class="dropdown-toggle"><i class="ph ph-database"></i> Master Data</a>
                            <ul class="collapse list-unstyled {{ request()->routeIs('admin.master.*') || request()->routeIs('admin.parts.*') || request()->routeIs('admin.lines.*') || request()->routeIs('admin.shifts.*') || request()->routeIs('admin.defects.*') || request()->routeIs('admin.downtimes.*') ? 'show' : '' }}" id="masterSubmenu">
                                <li><a href="{{ route('admin.master.lot-numbers.index') }}"><i class="ph ph-hash"></i> Lot Numbers</a></li>
                                <li><a href="{{ route('admin.master.activity-codes.index') }}"><i class="ph ph-code"></i> Activity Codes</a></li>
                                <li><a href="{{ route('admin.master.part-codes.index') }}"><i class="ph ph-gear-six"></i> Part Codes</a></li>
                                <li><a href="{{ route('admin.master.shifts.index') }}"><i class="ph ph-clock"></i> Shifts</a></li>
                                <li><a href="{{ route('admin.master.users.index') }}"><i class="ph ph-users"></i> Users</a></li>
                                <hr class="my-1">
                                <li><a href="{{ route('admin.parts.index') }}"><i class="ph ph-package"></i> Parts (Legacy)</a></li>
                                <li><a href="{{ route('admin.lines.index') }}"><i class="ph ph-list"></i> Lines</a></li>
                                <li><a href="{{ route('admin.defects.index') }}"><i class="ph ph-warning"></i> Data Defect</a></li>
                                <li><a href="{{ route('admin.downtimes.index') }}"><i class="ph ph-stopwatch"></i> Data Downtime</a></li>
                            </ul>
                        </li>
                        <h6 class="sidebar-heading px-3 mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 0.75rem;">Sistem</h6>
                        <li class="{{ request()->routeIs('admin.audit.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.audit.index') }}"><i class="ph ph-clock-counter-clockwise"></i> Audit Trail</a>
                        </li>
                    @elseif(auth()->user()->hasRole('karyawan'))
                        <li class="{{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('operator.dashboard') }}"><i class="ph ph-house"></i> Dashboard</a>
                        </li>
                        <li class="{{ request()->routeIs('operator.op-record.*') ? 'active' : '' }}">
                            <a href="{{ route('operator.op-record.index') }}"><i class="ph ph-clipboard-text"></i> My Records</a>
                        </li>
                        <li class="{{ request()->routeIs('operator.op-record.create') ? 'active' : '' }}">
                            <a href="{{ route('operator.op-record.create') }}"><i class="ph ph-plus-circle"></i> New Record</a>
                        </li>
                        <li class="{{ request()->routeIs('operator.horenzo.*') ? 'active' : '' }}">
                            <a href="{{ route('operator.horenzo.index') }}"><i class="ph ph-file-text"></i> Horenzo Reports</a>
                        </li>
                        <li class="{{ request()->routeIs('operator.report-record.*') ? 'active' : '' }}">
                            <a href="{{ route('operator.report-record.index') }}"><i class="ph ph-export"></i> Report All Record</a>
                        </li>
                        <hr class="my-1">
                        <li class="small text-muted px-3">Legacy</li>
                        <li class="{{ request()->routeIs('operator.lot.*') || request()->routeIs('operator.record.*') || request()->routeIs('operator.api.*') ? 'active' : '' }}">
                            <a href="#"><i class="ph ph-play-circle"></i> Lot Execution (Old)</a>
                        </li>
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
                        <i class="ph ph-user-circle text-primary fs-5 align-middle me-1"></i> {{ Auth::user()->name ?? 'User' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="border-radius: 12px;">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger fw-500 py-2"><i class="ph ph-sign-out me-2"></i> Logout</button>
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

    <div class="modal fade" id="confirmModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
            <div class="modal-content border-0 shadow-lg" style="border-radius:12px;">
                <div class="modal-body text-center p-4">
                    <div id="confirmModalIcon" class="mb-3" style="font-size:2.5rem;line-height:1;"></div>
                    <h6 id="confirmModalTitle" class="fw-bold mb-2"></h6>
                    <p id="confirmModalMessage" class="text-muted small mb-0"></p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light fw-bold px-4" id="confirmModalCancel" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn fw-bold px-4" id="confirmModalOk">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let confirmResolve = null;

        $('#confirmModal').on('hidden.bs.modal', function () {
            if (confirmResolve) { confirmResolve(false); confirmResolve = null; }
        });

        $('#confirmModalOk').on('click', function () {
            if (confirmResolve) { confirmResolve(true); confirmResolve = null; }
            $('#confirmModal').modal('hide');
        });

        $('#confirmModalCancel').on('click', function () {
            if (confirmResolve) { confirmResolve(false); confirmResolve = null; }
        });

        function showConfirm(options) {
            const modal = $('#confirmModal');
            const icon = options.icon || 'warning';
            const iconColor = options.danger ? '#dc2626' : '#f59e0b';
            const iconMap = {
                warning: '<i class="ph ph-warning-circle" style="color:' + iconColor + '"></i>',
                trash: '<i class="ph ph-trash" style="color:#dc2626"></i>',
                check: '<i class="ph ph-check-circle" style="color:#16a34a"></i>',
                info: '<i class="ph ph-info" style="color:#3b82f6"></i>',
            };

            $('#confirmModalIcon').html(iconMap[icon] || iconMap.warning);
            $('#confirmModalTitle').text(options.title || 'Konfirmasi');
            $('#confirmModalMessage').text(options.message || '');

            const $okBtn = $('#confirmModalOk');
            $okBtn.text(options.confirmText || 'Ya, Lanjutkan');
            $okBtn.removeClass('btn-danger btn-primary btn-success');
            $okBtn.addClass(options.danger ? 'btn-danger' : 'btn-primary');

            return new Promise(function (resolve) {
                confirmResolve = resolve;
                modal.modal('show');
            });
        }

        $(document).on('click', '[data-confirm]', function (e) {
            e.preventDefault();
            const $btn = $(this);
            const message = $btn.data('confirm');
            const danger = $btn.data('confirm-danger') !== undefined;
            const $form = $btn.closest('form');

            showConfirm({
                title: 'Konfirmasi',
                message: message,
                confirmText: $btn.data('confirm-yes') || 'Ya, Lanjutkan',
                danger: danger,
                icon: danger ? 'trash' : 'warning',
            }).then(function (ok) {
                if (ok) $form.trigger('submit');
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
