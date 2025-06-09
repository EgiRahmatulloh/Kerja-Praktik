<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title') - Sistem Surat Menyurat</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="{{ asset('favicon.ico') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('dashmin/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashmin/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('dashmin/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('dashmin/css/style.css') }}" rel="stylesheet">
    <style>
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
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3" style="overflow-y: auto; max-height: 100vh;">
            <nav class="navbar bg-light navbar-light">
                <a href="{{ route('admin.dashboard') }}" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary">Admin Panel</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="{{ asset('dashmin/img/user.jpg') }}" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                        <span>Admin</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="{{ route('admin.dashboard') }}" class="nav-item nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('admin.templates.index') }}" class="nav-item nav-link {{ request()->routeIs('admin.templates.*') ? 'active' : '' }}">
                        <i class="fa fa-file-alt me-2"></i>Template Surat
                    </a>
                    <a href="{{ route('admin.data-items.index') }}" class="nav-item nav-link {{ request()->routeIs('admin.data-items.*') ? 'active' : '' }}">
                        <i class="fa fa-list-alt me-2"></i>Variabel Surat
                    </a>
                    <a href="{{ route('admin.letter-types.index') }}" class="nav-item nav-link {{ request()->routeIs('admin.letter-types.*') ? 'active' : '' }}">
                        <i class="fa fa-file-word me-2"></i>Jenis Surat
                    </a>
                    <a href="{{ route('admin.service-schedules.index') }}" class="nav-item nav-link {{ request()->routeIs('admin.service-schedules.*') ? 'active' : '' }}">
                        <i class="fa fa-clock me-2"></i>Jadwal Pelayanan
                    </a>
                    <a href="{{ route('admin.letters.index') }}" class="nav-item nav-link {{ request()->routeIs('admin.letters.*') ? 'active' : '' }}">
                        <i class="fa fa-plus-square me-2"></i>Buat Surat
                    </a>
                    <a href="{{ route('admin.filled-letters.index') }}" class="nav-item nav-link {{ request()->routeIs('admin.filled-letters.*') ? 'active' : '' }}">
                        <i class="fa fa-envelope-open-text me-2"></i>Pengajuan Surat
                    </a>
                    <a href="{{ route('admin.letter-queues.index') }}" class="nav-item nav-link {{ request()->routeIs('admin.letter-queues.*') ? 'active' : '' }}">
                        <i class="fa fa-hourglass-half me-2"></i>Antrian Surat
                    </a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="{{ asset('dashmin/img/user.jpg') }}" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex">{{ Auth::user()->name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

            @yield('content')

            <!-- Footer Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">Sistem Surat Menyurat</a>, All Right Reserved.
                        </div>
                        <div class="col-12 col-sm-6 text-center text-sm-end">
                            Designed By <a href="https://htmlcodex.com">HTML Codex</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- Notification Container -->
    <div id="notification-container"></div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('dashmin/lib/chart/chart.min.js') }}"></script>
    <script src="{{ asset('dashmin/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('dashmin/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('dashmin/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('dashmin/lib/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('dashmin/lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('dashmin/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('dashmin/js/main.js') }}"></script>

    @yield('scripts')

    <!-- Notification Sound -->
    <audio id="notificationSound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>

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

            // Putar suara notifikasi
            const notificationSound = document.getElementById('notificationSound');
            if (notificationSound) {
                notificationSound.play();
            }

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

            // Toggle Sidebar (kode ini tidak ada di versi awal admin, jadi tidak perlu dikembalikan)
            // const sidebarToggle = document.getElementById('sidebarToggle');
            // const sidebar = document.getElementById('sidebar');
            // const mainContent = document.getElementById('mainContent');
            // const toggleIcon = document.getElementById('toggleIcon');

            // // Cek apakah status sidebar tersimpan di localStorage
            // const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            // // Terapkan status sidebar dari localStorage jika ada
            // if (sidebarCollapsed) {
            //     sidebar.classList.add('collapsed');
            //     mainContent.classList.add('expanded');
            //     sidebarToggle.classList.add('collapsed');
            // }

            // sidebarToggle.addEventListener('click', function() {
            //     sidebar.classList.toggle('collapsed');
            //     mainContent.classList.toggle('expanded');
            //     sidebarToggle.classList.toggle('collapsed');

            //     // Simpan status sidebar di localStorage
            //     if (sidebar.classList.contains('collapsed')) {
            //         localStorage.setItem('sidebarCollapsed', 'true');
            //     } else {
            //         localStorage.setItem('sidebarCollapsed', 'false');
            //     }
            // });
        });
    </script>
    <!-- Tambahkan di bagian script sebelum </body> -->
    <script>
        // Kode notifikasi yang sudah ada

        // Tambahkan kode berikut untuk mendengarkan event Pusher
        $(document).ready(function() {
            // Kode polling yang sudah ada

            // Dengarkan event real-time dari Pusher
            if (window.Echo && typeof window.Echo.channel === 'function') {
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
            } else {
                console.warn('Laravel Echo is not loaded. Real-time notifications will not work.');
            }
        });
    </script>
</body>

</html>
