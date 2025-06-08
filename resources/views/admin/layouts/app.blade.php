<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: 60px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
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
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 60px;
        }

        .navbar {
            margin-left: 250px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .navbar.expanded {
            margin-left: 60px;
        }

        .sidebar-toggle {
            position: fixed;
            top: 10px;
            left: 260px;
            z-index: 101;
            background-color: #343a40;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sidebar-toggle.collapsed {
            left: 70px;
        }

        .sidebar-toggle:focus {
            outline: none;
        }

        .sidebar.collapsed .text-center p,
        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .text-center h4 {
            font-size: 0;
            margin-bottom: 30px;
        }

        .sidebar.collapsed .text-center h4::first-letter {
            font-size: 24px;
        }

        /* Notification Styles */
        #notification-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        }

        .notification {
            background-color: #fff;
            border-left: 4px solid #28a745;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 10px;
            animation: slideIn 0.3s ease-out;
            position: relative;
            transition: all 0.3s ease;
        }

        .notification.closing {
            transform: translateX(400px);
            opacity: 0;
        }

        .notification-title {
            font-weight: bold;
            margin-bottom: 5px;
            padding-right: 20px;
        }

        .notification-message {
            color: #555;
        }

        .notification-close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 16px;
            color: #999;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="bi bi-list" id="toggleIcon"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="text-center mb-4">
            <h4>Admin Panel</h4>
            <p class="text-muted">{{ Auth::user()->name }}</p>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.templates.*') ? 'active' : '' }}" href="{{ route('admin.templates.index') }}">
                    <i class="bi bi-file-earmark-text"></i> <span>Template Surat</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.data-items.*') ? 'active' : '' }}" href="{{ route('admin.data-items.index') }}">
                    <i class="bi bi-list-check"></i> <span>Variabel Surat</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.letter-types.*') ? 'active' : '' }}" href="{{ route('admin.letter-types.index') }}">
                    <i class="bi bi-file-earmark-richtext"></i> <span>Jenis Surat</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.service-schedules.*') ? 'active' : '' }}" href="{{ route('admin.service-schedules.index') }}">
                    <i class="fa fa-clock"></i> <span>Jadwal Pelayanan</span>
                </a>
            </li>
            <!-- Menu pengumuman telah dihapus -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.filled-letters.*') ? 'active' : '' }}" href="{{ route('admin.filled-letters.index') }}">
                    <i class="bi bi-envelope"></i> <span>Pengajuan Surat</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.letter-queues.*') ? 'active' : '' }}" href="{{ route('admin.letter-queues.index') }}">
                    <i class="bi bi-hourglass-split"></i> <span>Antrian Surat</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        @yield('content')
    </div>

    <!-- Ubah urutan script ini -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Notification Container -->
    <div id="notification-container"></div>

    @yield('scripts')

    <!-- Notification Script -->
    <script>
        // Fungsi untuk menampilkan notifikasi
        function showNotification(title, message) {
            const notificationContainer = document.getElementById('notification-container');
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerHTML = `
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
                <div class="notification-close">&times;</div>
            `;

            notificationContainer.appendChild(notification);

            // Tambahkan event listener untuk tombol close
            const closeButton = notification.querySelector('.notification-close');
            closeButton.addEventListener('click', function() {
                closeNotification(notification);
            });

            // Otomatis tutup notifikasi setelah 7 detik
            setTimeout(function() {
                closeNotification(notification);
            }, 7000);
        }

        // Fungsi untuk menutup notifikasi dengan animasi
        function closeNotification(notification) {
            notification.classList.add('closing');
            setTimeout(function() {
                notification.remove();
            }, 300);
        }

        // Polling untuk memeriksa notifikasi baru setiap 10 detik
        function checkNewNotifications() {
            $.ajax({
                url: '{{ route("admin.notifications.check") }}',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.notifications && response.notifications.length > 0) {
                        response.notifications.forEach(function(notification) {
                            showNotification(notification.title, notification.message);
                        });
                    }
                }
            });
        }

        // Mulai polling saat halaman dimuat
        $(document).ready(function() {
            // Setup AJAX untuk CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Periksa notifikasi pertama kali
            checkNewNotifications();

            // Kemudian periksa setiap 10 detik
            setInterval(checkNewNotifications, 10000);

            // Toggle Sidebar
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('toggleIcon');

            // Cek apakah status sidebar tersimpan di localStorage
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            // Terapkan status sidebar dari localStorage jika ada
            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                sidebarToggle.classList.add('collapsed');
            }

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                sidebarToggle.classList.toggle('collapsed');

                // Simpan status sidebar di localStorage
                if (sidebar.classList.contains('collapsed')) {
                    localStorage.setItem('sidebarCollapsed', 'true');
                } else {
                    localStorage.setItem('sidebarCollapsed', 'false');
                }
            });
        });
    </script>
    <!-- Tambahkan di bagian script sebelum </body> -->
    <script>
        // Kode notifikasi yang sudah ada

        // Tambahkan kode berikut untuk mendengarkan event Pusher
        $(document).ready(function() {
            // Kode polling yang sudah ada

            // Dengarkan event real-time dari Pusher
            window.Echo.channel('admin-notifications')
                .listen('.letter.submitted', (data) => {
                    showNotification(data.title, data.message, data.url);

                    // Update last_seen_letter_id di session
                    $.ajax({
                        url: '{{ route("admin.notifications.mark-all-read") }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        dataType: 'json'
                    });
                });
        });
    </script>
</body>

</html>