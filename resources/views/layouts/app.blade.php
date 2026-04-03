<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>E-Notulen - @yield('title')</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        @include('layouts.navigation')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter" id="notification-counter"
                                    style="display: none;">0</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                style="width: 350px;" aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span>Notifications</span>
                                    <small>
                                        <a href="#" id="mark-all-read" class="text-primary"
                                            style="font-size: 12px;">Mark all as read</a>
                                    </small>
                                </h6>
                                <div id="notifications-container">
                                    <div class="text-center py-4">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        <p class="small text-muted mb-0">Loading notifications...</p>
                                    </div>
                                </div>
                                <a class="dropdown-item text-center small text-gray-500"
                                    href="{{ route('notifications.index') }}">Show All
                                    Notifications</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span
                                    class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                                <img class="img-profile rounded-circle" src="{{ asset('img/undraw_profile.svg') }}">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                {{-- <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a> --}}
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">@yield('title')</h1>
                    </div>
                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; E-Notulen 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- Page level plugins -->
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Load notifications when page loads
            loadNotifications();

            // Load notifications when dropdown is clicked
            $('#alertsDropdown').on('click', function() {
                loadNotifications();
            });

            // Mark all as read
            $(document).on('click', '#mark-all-read', function(e) {
                e.preventDefault();
                markAllAsRead();
            });

            // Mark individual notification as read
            $(document).on('click', '.notification-item', function(e) {
                e.preventDefault();
                const notificationId = $(this).data('id');
                const isRead = $(this).data('read');

                if (!isRead) {
                    markAsRead(notificationId);
                }
            });
        });

        function loadNotifications() {
            $.ajax({
                url: '{{ route('notifications.index') }}',
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        displayNotifications(response.data);
                        updateNotificationCounter(response.unread_count);
                    }
                },
                error: function(xhr) {
                    console.error('Error loading notifications:', xhr);
                    $('#notifications-container').html(`
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            <p class="small text-muted mb-0">Failed to load notifications</p>
                        </div>
                    `);
                }
            });
        }

        function displayNotifications(notifications) {
            let html = '';

            if (notifications.length === 0) {
                html = `
                    <div class="text-center py-4">
                        <i class="fas fa-bell-slash text-muted"></i>
                        <p class="small text-muted mb-0">No notifications</p>
                    </div>
                `;
            } else {
                notifications.forEach(function(notification) {
                    const iconClass = getNotificationIcon(notification.type);
                    const iconColor = getNotificationColor(notification.type);
                    const readClass = notification.is_read ? 'text-muted' : '';
                    const readIndicator = notification.is_read ? '' :
                        '<span class="badge badge-primary badge-sm ml-2">New</span>';

                    html += `
                        <a class="dropdown-item d-flex align-items-center notification-item ${readClass}" 
                           href="#" 
                           data-id="${notification.id}" 
                           data-read="${notification.is_read}">
                            <div class="mr-3">
                                <div class="icon-circle ${iconColor}">
                                    <i class="${iconClass} text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-gray-500">${notification.time_ago}</div>
                                <div class="font-weight-bold" style="font-size: 0.875rem;">
                                    ${notification.title}
                                    ${readIndicator}
                                </div>
                                <div class="small text-truncate" style="max-width: 200px;">
                                    ${notification.message}
                                </div>
                            </div>
                        </a>
                    `;
                });
            }

            $('#notifications-container').html(html);
        }

        function getNotificationIcon(type) {
            switch (type) {
                case 'meeting':
                    return 'fas fa-calendar';
                case 'approval':
                    return 'fas fa-check-circle';
                case 'reminder':
                    return 'fas fa-clock';
                case 'logistics':
                    return 'fas fa-truck';
                case 'system':
                    return 'fas fa-cog';
                default:
                    return 'fas fa-bell';
            }
        }

        function getNotificationColor(type) {
            switch (type) {
                case 'meeting':
                    return 'bg-primary';
                case 'approval':
                    return 'bg-success';
                case 'reminder':
                    return 'bg-warning';
                case 'logistics':
                    return 'bg-info';
                case 'system':
                    return 'bg-secondary';
                default:
                    return 'bg-primary';
            }
        }

        function updateNotificationCounter(count) {
            const counter = $('#notification-counter');
            if (count > 0) {
                counter.text(count > 99 ? '99+' : count).show();
            } else {
                counter.hide();
            }
        }

        function markAsRead(notificationId) {
            $.ajax({
                url: `/notifications/${notificationId}/mark-as-read`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        loadNotifications(); // Reload to update UI
                        Swal.fire({
                            icon: 'success',
                            title: 'Notification marked as read',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error marking notification as read:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to mark notification as read',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                }
            });
        }

        function markAllAsRead() {
            $.ajax({
                url: '{{ route('notifications.mark-all-as-read') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status) {
                        loadNotifications(); // Reload to update UI
                        toastr.success('All notifications marked as read');
                    }
                },
                error: function(xhr) {
                    console.error('Error marking all notifications as read:', xhr);
                    toastr.error('Failed to mark all notifications as read');
                }
            });
        }

        // Auto refresh notifications every 30 seconds
        setInterval(function() {
            loadNotifications();
        }, 30000);
    </script>

    @stack('scripts')

</body>

</html>
