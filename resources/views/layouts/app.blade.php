<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Sistem Manajemen Surat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: #fff;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            z-index: 100;
            padding-top: 20px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            margin-bottom: 5px;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .navbar {
            margin-left: 250px;
            padding: 10px 20px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4>Sistem Manajemen Surat</h4>
            <p class="text-muted">{{ Auth::user()->name }}</p>
        </div>
        <ul class="nav flex-column">
            @if(Auth::user()->role === 'admin')
            <!-- Menu Admin -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.templates.*') ? 'active' : '' }}" href="{{ route('admin.templates.index') }}">
                    <i class="bi bi-file-earmark-text"></i> Template Surat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.data-items.*') ? 'active' : '' }}" href="{{ route('admin.data-items.index') }}">
                    <i class="bi bi-list-check"></i> Variabel Surat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.letter-types.*') ? 'active' : '' }}" href="{{ route('admin.letter-types.index') }}">
                    <i class="bi bi-file-earmark-richtext"></i> Jenis Surat
                </a>
            </li>
            <!-- Di dalam menu admin (sekitar baris 75, setelah menu Antrian Surat) -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.service-schedules.*') ? 'active' : '' }}" href="{{ route('admin.service-schedules.index') }}">
                    <i class="bi bi-clock"></i> Jadwal Pelayanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.filled-letters.*') ? 'active' : '' }}" href="{{ route('admin.filled-letters.index') }}">
                    <i class="bi bi-envelope"></i> Pengajuan Surat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.letter-queues.*') ? 'active' : '' }}" href="{{ route('admin.letter-queues.index') }}">
                    <i class="bi bi-hourglass-split"></i> Antrian Surat
                </a>
            </li>
            @else
            <!-- Menu User -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.letters.create') ? 'active' : '' }}" href="{{ route('user.letters.create') }}">
                    <i class="bi bi-file-earmark-plus"></i> Buat Surat Baru
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('user.letters.index') ? 'active' : '' }}" href="{{ route('user.letters.index') }}">
                    <i class="bi bi-file-earmark-text"></i> Daftar Surat Saya
                </a>
            </li>
            @endif
            <li class="nav-item mt-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link border-0 bg-transparent">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
            <div class="container-fluid">
                <h4 class="mb-0">@yield('title')</h4>
            </div>
        </nav>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <script>
        $(document).ready(function() {
            // Setup AJAX untuk CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
</body>

</html>