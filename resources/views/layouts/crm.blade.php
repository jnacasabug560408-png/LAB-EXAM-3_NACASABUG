<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - InnEase CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #1f3b57;
            --secondary-color: #2e86de;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #eef2f6;
        }

        .wrapper { display: flex; min-height: 100vh; }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #12222f 100%);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar .logo {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .tenant-tag {
            font-size: 12px;
            color: rgba(255,255,255,0.65);
            margin-bottom: 25px;
            display: block;
        }

        .sidebar .nav-menu { list-style: none; }
        .sidebar .nav-menu li { margin-bottom: 6px; }
        .sidebar .nav-section {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.45);
            margin: 18px 0 8px 15px;
        }

        .sidebar .nav-menu a {
            color: rgba(255,255,255,0.82);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.25s;
            font-size: 14px;
        }

        .sidebar .nav-menu a:hover,
        .sidebar .nav-menu a.active {
            background-color: var(--secondary-color);
            color: white;
        }

        .main-content { margin-left: 250px; flex: 1; }

        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            padding: 12px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .navbar-brand { font-weight: bold; color: var(--primary-color); }

        .context-switchers { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .context-switchers .form-select { min-width: 190px; }

        .content { padding: 25px; }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        /* Clickable KPI cards (drill-down) */
        .kpi-card {
            display: block;
            text-decoration: none;
            color: inherit;
            background: white;
            border-radius: 10px;
            border-left: 4px solid var(--secondary-color);
            padding: 18px 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }

        a.kpi-card { cursor: pointer; }

        a.kpi-card:hover,
        a.kpi-card:focus {
            transform: translateY(-5px);
            box-shadow: 0 10px 22px rgba(31, 59, 87, 0.18);
            border-left-color: var(--primary-color);
        }

        a.kpi-card:hover .kpi-drill { opacity: 1; transform: translateX(0); }

        .kpi-card .kpi-label { color: #7f8c8d; font-size: 13px; margin: 0; }
        .kpi-card .kpi-value { font-size: 28px; font-weight: 700; color: var(--primary-color); margin: 6px 0 0; }
        .kpi-card .kpi-icon { font-size: 26px; color: var(--secondary-color); }
        .kpi-card .kpi-drill {
            font-size: 12px;
            color: var(--secondary-color);
            opacity: 0;
            transform: translateX(-6px);
            transition: all 0.25s ease;
        }

        .table { background: white; border-radius: 8px; overflow: hidden; }
        .table thead { background-color: var(--primary-color); color: white; }
        .table tbody tr:hover { background-color: #f5f7fa; }

        .badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; }

        .alert { border: none; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background-color: #d4edda; color: #155724; border-left: 4px solid var(--success-color); }
        .alert-danger { background-color: #f8d7da; color: #721c24; border-left: 4px solid var(--danger-color); }

        .btn { border-radius: 6px; font-weight: 500; }
        .btn-primary { background-color: var(--secondary-color); border-color: var(--secondary-color); }
        .btn-primary:hover { background-color: #1f6fb8; border-color: #1f6fb8; }

        .form-label { font-weight: 500; color: var(--primary-color); }

        .board-column { background: #e4e9f0; border-radius: 10px; padding: 12px; min-height: 200px; }
        .board-column h6 { font-weight: 700; color: var(--primary-color); }
        .board-card { background: white; border-radius: 8px; padding: 12px; margin-bottom: 10px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }

        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; }
            .wrapper { flex-direction: column; }
        }
    </style>
    @yield('styles')
</head>
<body>
@php
    $user = auth()->user();
    $tenant = $tenantContext->tenant();
    $code = $tenant?->code;
    $isMaster = $tenantContext->isMaster();
    $globalView = $tenantContext->isGlobalView();
@endphp
<div class="wrapper">
    <aside class="sidebar">
        <div class="logo">
            <i class="bi bi-buildings"></i>
            <span>InnEase CRM</span>
        </div>
        <span class="tenant-tag">{{ $tenantContext->label() }}</span>

        <ul class="nav-menu">
            <li>
                <a href="{{ route('crm.dashboard') }}" class="{{ request()->routeIs('crm.dashboard', 'dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i><span>Dashboard</span>
                </a>
            </li>

            @if($isMaster)
                <li class="nav-section">Platform</li>
                <li>
                    <a href="{{ route('master.tenants.index') }}" class="{{ request()->routeIs('master.tenants.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3"></i><span>Tenant Admin</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('master.subscriptions.index') }}" class="{{ request()->routeIs('master.subscriptions.*') ? 'active' : '' }}">
                        <i class="bi bi-credit-card"></i><span>Subscriptions</span>
                    </a>
                </li>
            @endif

            @unless($globalView)
                <li class="nav-section">Transactions</li>
                <li>
                    <a href="{{ route('reservations.index') }}" class="{{ request()->routeIs('reservations.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check"></i><span>Reservations</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin"></i><span>Point of Sale</span>
                    </a>
                </li>

                <li class="nav-section">Data Collection</li>
                <li>
                    <a href="{{ route('guests.index') }}" class="{{ request()->routeIs('guests.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i><span>Guests</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('feedback.index') }}" class="{{ request()->routeIs('feedback.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-left-text"></i><span>Feedback</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('interactions.index') }}" class="{{ request()->routeIs('interactions.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i><span>Interaction Logs</span>
                    </a>
                </li>

                <li class="nav-section">Management</li>
                <li>
                    <a href="{{ route('actions.index') }}" class="{{ request()->routeIs('actions.*') ? 'active' : '' }}">
                        <i class="bi bi-list-task"></i><span>Actions</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.sales') }}" class="{{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                        <i class="bi bi-graph-up-arrow"></i><span>Sales Report</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('promotions.index') }}" class="{{ request()->routeIs('promotions.*') ? 'active' : '' }}">
                        <i class="bi bi-megaphone"></i><span>Promotions</span>
                    </a>
                </li>
                @if($tenantContext->supportsBranching())
                    <li>
                        <a href="{{ route('branches.index') }}" class="{{ request()->routeIs('branches.*') ? 'active' : '' }}">
                            <i class="bi bi-building"></i><span>Branches</span>
                        </a>
                    </li>
                @endif
            @endunless

            <li style="margin-top: 25px; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 15px;">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-link" style="width:100%; text-align:left; color: rgba(255,255,255,0.8); padding: 10px 15px; text-decoration:none;">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <nav class="navbar">
            <div class="navbar-brand">@yield('page-title', 'Dashboard')</div>

            <div class="context-switchers">
                @if($isMaster)
                    <form action="{{ route('context.tenant') }}" method="POST" class="d-flex align-items-center gap-2">
                        @csrf
                        <label class="mb-0 text-muted small"><i class="bi bi-diagram-3"></i> Tenant</label>
                        <select name="tenant_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Master (All Tenants)</option>
                            @foreach($availableTenants as $option)
                                <option value="{{ $option->id }}" @selected($tenant?->id === $option->id)>
                                    {{ $option->name }} ({{ $option->code }}){{ $option->isActive() ? '' : ' — suspended' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                @if($tenantContext->supportsBranching())
                    <form action="{{ route('context.branch') }}" method="POST" class="d-flex align-items-center gap-2">
                        @csrf
                        <label class="mb-0 text-muted small"><i class="bi bi-building"></i> Branch</label>
                        <select name="branch_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Branches</option>
                            @foreach($availableBranches as $branchOption)
                                <option value="{{ $branchOption->id }}" @selected($tenantContext->branchId() === $branchOption->id)>
                                    {{ $branchOption->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                <div class="d-flex align-items-center gap-2">
                    <div class="text-end">
                        <div style="font-size: 14px; font-weight: 600;">{{ $user->name }}</div>
                        <div class="text-muted" style="font-size: 12px;">{{ ucfirst($user->role) }}</div>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2e86de&color=fff"
                         alt="Avatar" style="width: 38px; height: 38px; border-radius: 50%;">
                </div>
            </div>
        </nav>

        <div class="content">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@yield('scripts')
@stack('scripts')
</body>
</html>
