<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Absensi Karyawan')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --primary-blue: #1e40af;
            --primary-blue-dark: #1e3a8a;
            --primary-blue-light: #3b82f6;
            --sidebar-width: 260px;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f5f9;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-blue) 0%, var(--primary-blue-dark) 100%);
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand h4 {
            color: #fff;
            font-weight: 700;
            margin: 0;
        }
        .sidebar-brand small {
            color: rgba(255,255,255,0.7);
            font-size: 0.75rem;
        }
        .sidebar-menu {
            padding: 1rem 0;
        }
        .sidebar-menu .menu-header {
            padding: 0.5rem 1.5rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.5);
            font-weight: 600;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.85);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,.1);
            border-left-color: var(--primary-blue-light);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,.15);
            border-left-color: #fff;
        }
        .sidebar .nav-link i {
            width: 24px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            background: rgba(0,0,0,0.1);
        }
        .user-panel {
            display: flex;
            align-items: center;
            padding: 0.5rem;
        }
        .user-panel img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .user-panel .user-info {
            margin-left: 0.75rem;
            flex: 1;
        }
        .user-panel .user-info .name {
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            line-height: 1.2;
        }
        .user-panel .user-info .role {
            color: rgba(255,255,255,0.7);
            font-size: 0.75rem;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            padding: 1rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .topbar h5 {
            margin: 0;
            font-weight: 600;
            color: var(--primary-blue-dark);
        }
        .content-wrapper {
            padding: 1.5rem;
        }
        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 0.5rem;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            padding: 1rem 1.25rem;
        }
        .btn-primary {
            background: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        .btn-primary:hover {
            background: var(--primary-blue-dark);
            border-color: var(--primary-blue-dark);
        }
        .badge-pending { background: #f59e0b; }
        .badge-approved { background: #10b981; }
        .badge-rejected { background: #ef4444; }
        .stat-card {
            border-radius: 0.75rem;
            padding: 1.25rem;
            color: #fff;
        }
        .stat-card.blue { background: linear-gradient(135deg, #3b82f6, #1e40af); }
        .stat-card.green { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-card.yellow { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-card.red { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .stat-card .stat-icon {
            font-size: 2.5rem;
            opacity: 0.3;
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }
        .avatar-lg {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
        }
        .avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h4><i class="bi bi-clock-history"></i> AbsensiPro</h4>
            <small>Sistem Absensi Karyawan</small>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-header">Menu Utama</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}" href="{{ route('attendances.index') }}">
                        <i class="bi bi-calendar-check"></i> Riwayat Absensi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('leaves.*') ? 'active' : '' }}" href="{{ route('leaves.index') }}">
                        <i class="bi bi-calendar-x"></i> Izin / Cuti
                    </a>
                </li>
            </ul>

            @if(auth()->user()->isAdmin())
            <div class="menu-header mt-3">Manajemen</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                        <i class="bi bi-people"></i> Karyawan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}" href="{{ route('locations.index') }}">
                        <i class="bi bi-geo-alt"></i> Lokasi Kerja
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('attendance.today') ? 'active' : '' }}" href="{{ route('attendance.today') }}">
                        <i class="bi bi-calendar-day"></i> Absensi Hari Ini
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                    </a>
                </li>
            </ul>
            @endif

            <div class="menu-header mt-3">Pengaturan</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}">
                        <i class="bi bi-person-gear"></i> Profil Saya
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="user-panel">
                @php $employee = auth()->user()->employee; @endphp
                <img src="{{ $employee && $employee->photo ? asset('storage/'.$employee->photo) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=random' }}" alt="Avatar">
                <div class="user-info">
                    <div class="name">{{ auth()->user()->name }}</div>
                    <div class="role">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Karyawan' }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="ms-2">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="topbar">
            <h5>@yield('page-title', 'Dashboard')</h5>
            <div class="d-flex align-items-center">
                <span class="text-muted me-3">
                    <i class="bi bi-calendar3"></i> {{ now()->format('l, d F Y') }}
                </span>
            </div>
        </div>

        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @stack('scripts')
</body>
</html>
