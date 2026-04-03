@extends('layouts.app')
@section('title', 'Dashboard')
@push('styles')
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css">
    <style>
        /* Custom styles for Google Calendar-like appearance */
        .sidebar {
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
            height: 100vh;
            overflow-y: auto;
        }

        .mini-calendar {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 20px;
        }

        .calendar-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 20px 0;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .view-buttons .btn {
            margin-left: 5px;
        }

        /* FullCalendar customizations */
        .fc-toolbar {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .fc-event {
            border: none !important;
            padding: 2px 6px;
            border-radius: 4px;
            cursor: pointer;
        }

        .fc-daygrid-event {
            margin: 1px 0;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -250px;
                width: 250px;
                transition: left 0.3s;
                z-index: 1000;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .calendar-header {
                flex-direction: column;
                align-items: stretch;
            }

            .view-buttons {
                margin-top: 10px;
            }
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-toggle {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar-toggle {
                display: block;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-0">
        <div class="row no-gutters">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar" id="sidebar">
                <!-- Mobile close button -->
                <div class="d-md-none text-right p-3">
                    <button class="btn btn-sm btn-outline-secondary" id="closeSidebar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Create Event Button -->
                @if (Auth::user()->role && Auth::user()->role->name === 'sekretariat')
                    <div class="p-3">
                        <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#eventModal">
                            <i class="fas fa-plus"></i> Buat Rapat
                        </button>
                    </div>
                @endif

                <!-- Mini Calendar -->
                <div class="px-3">
                    <div class="mini-calendar">
                        <h6 class="text-center mb-3">Kalender Mini</h6>
                        <div id="mini-calendar"></div>
                    </div>
                </div>

                <!-- My Calendars -->
                <div class="px-3">
                    <h6>Kalender Saya</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="calendar1" checked>
                        <label class="form-check-label" for="calendar1">
                            <span class="badge badge-primary mr-2">●</span> Rapat Paripurna
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="calendar2" checked>
                        <label class="form-check-label" for="calendar2">
                            <span class="badge badge-success mr-2">●</span> Rapat Komisi
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="calendar3" checked>
                        <label class="form-check-label" for="calendar3">
                            <span class="badge badge-info mr-2">●</span> Rapat Badan
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="calendar4" checked>
                        <label class="form-check-label" for="calendar4">
                            <span class="badge badge-warning mr-2">●</span> Lain-lainnya
                        </label>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content" style="margin-left: auto;">
                <!-- Mobile sidebar toggle -->
                <div class="d-md-none p-3">
                    <button class="btn btn-outline-secondary sidebar-toggle" id="sidebarToggle">
                        <i class="fas fa-bars"></i> Menu
                    </button>
                </div>

                <!-- Calendar Header -->
                <div class="calendar-header px-3">
                    <div class="d-flex align-items-center">
                        <h2 id="calendarTitle">Kalender Rapat</h2>
                        <button class="btn btn-outline-primary ml-3" id="todayBtn">Hari Ini</button>
                    </div>
                    <div class="view-buttons">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary" id="monthView">Bulan</button>
                            <button type="button" class="btn btn-outline-secondary" id="weekView">Minggu</button>
                            <button type="button" class="btn btn-outline-secondary" id="dayView">Hari</button>
                        </div>
                    </div>
                </div>

                <!-- Calendar Container -->
                <div class="calendar-container mx-3">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div class="overlay" id="overlay"></div>

    <!-- Event Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalTitle">Tambah Rapat Baru</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="eventForm">
                        <input type="hidden" id="eventId" />

                        <div class="form-group">
                            <label for="eventTitle">Judul Rapat *</label>
                            <input type="text" class="form-control" id="eventTitle" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="eventDate">Tanggal *</label>
                                <input type="date" class="form-control" id="eventDate" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="eventTime">Waktu *</label>
                                <input type="time" class="form-control" id="eventTime" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="eventLocation">Lokasi</label>
                            <input type="text" class="form-control" id="eventLocation"
                                placeholder="Ruang rapat, Zoom, dll">
                        </div>

                        <div class="form-group">
                            <label for="eventCategory">Jenis Rapat</label>
                            <select class="form-control" id="eventCategory" required>
                                <option value="rapat_paripurna">Rapat Paripurna</option>
                                <option value="rapat_komisi">Rapat Komisi</option>
                                <option value="rapat_badan">Rapat Badan</option>
                                <option value="lain_lain">Lain-lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="eventDescription">Deskripsi</label>
                            <textarea class="form-control" id="eventDescription" rows="3" placeholder="Deskripsi atau agenda rapat..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="deleteEventBtn"
                        style="display: none;">Hapus</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveEventBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalTitle">Detail Rapat</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-muted">Informasi Rapat</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="font-weight-bold" width="30%">Judul:</td>
                                    <td id="detailTitle">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Tanggal:</td>
                                    <td id="detailDate">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Waktu:</td>
                                    <td id="detailTime">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Lokasi:</td>
                                    <td id="detailLocation">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Jenis Rapat:</td>
                                    <td id="detailCategory">-</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Deskripsi:</td>
                                    <td id="detailDescription">-</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Peserta Rapat</h6>
                            <div id="detailPeserta" class="border rounded p-3"
                                style="max-height: 300px; overflow-y: auto;">
                                <div class="text-center text-muted">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <p>Tidak ada data peserta</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    @if (Auth::user()->role && Auth::user()->role->name === 'sekretariat')
                        <button type="button" class="btn btn-primary" onclick="editRapat()">
                            <i class="fas fa-edit"></i> Edit Rapat
                        </button>
                    @endif
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
        integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous">
    </script>
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
        // Main calendar initialization function
        function initializeCalendar() {
            // Check if all required libraries are loaded
            if (typeof FullCalendar === 'undefined') {
                console.error('FullCalendar library not loaded, retrying...');
                setTimeout(initializeCalendar, 100);
                return;
            }

            if (typeof $ === 'undefined') {
                console.error('jQuery library not loaded, retrying...');
                setTimeout(initializeCalendar, 100);
                return;
            }

            console.log('📚 All libraries loaded successfully');

            // Check if Bootstrap is available
            if (typeof $.fn.modal === 'undefined') {
                console.warn('⚠️ Bootstrap modal not available, some features may not work');
            }
            // Load events data from database
            let eventsData = @json($calendarEvents ?? []);
            console.log('📊 Events loaded from database:', eventsData.length);

            // Add some dummy data if no events from database
            if (eventsData.length === 0) {
                eventsData = [{
                        id: '1',
                        title: 'Rapat Paripurna Bulanan',
                        start: '2025-09-28T09:00:00',
                        backgroundColor: '#007bff',
                        borderColor: '#007bff',
                        extendedProps: {
                            location: 'Ruang Sidang Utama',
                            description: 'Pembahasan APBD dan program kerja',
                            category: 'paripurna',
                            peserta_count: 25,
                            peserta: [{
                                    name: 'Dr. Ahmad Suryadi',
                                    jabatan: 'Ketua DPRD'
                                },
                                {
                                    name: 'Hj. Siti Aminah',
                                    jabatan: 'Wakil Ketua'
                                },
                                {
                                    name: 'Budi Santoso, S.H.',
                                    jabatan: 'Anggota Komisi I'
                                }
                            ]
                        }
                    },
                    {
                        id: '2',
                        title: 'Rapat Komisi Pendidikan',
                        start: '2025-09-30T14:00:00',
                        backgroundColor: '#28a745',
                        borderColor: '#28a745',
                        extendedProps: {
                            location: 'Ruang Komisi A',
                            description: 'Evaluasi program pendidikan daerah',
                            category: 'komisi',
                            peserta_count: 12,
                            peserta: [{
                                    name: 'Prof. Indira Sari',
                                    jabatan: 'Ketua Komisi'
                                },
                                {
                                    name: 'Drs. Agus Wibowo',
                                    jabatan: 'Sekretaris'
                                },
                                {
                                    name: 'Rina Handayani, M.Pd.',
                                    jabatan: 'Anggota'
                                }
                            ]
                        }
                    },
                    {
                        id: '3',
                        title: 'Rapat Badan Anggaran',
                        start: '2025-10-02T10:00:00',
                        backgroundColor: '#17a2b8',
                        borderColor: '#17a2b8',
                        extendedProps: {
                            location: 'Ruang Badan Anggaran',
                            description: 'Review anggaran semester II',
                            category: 'badan',
                            peserta_count: 8,
                            peserta: [{
                                    name: 'H. Rahmat Hidayat',
                                    jabatan: 'Ketua Badan'
                                },
                                {
                                    name: 'Dewi Kartika, S.E.',
                                    jabatan: 'Bendahara'
                                }
                            ]
                        }
                    }
                ];
                console.log('📝 Using dummy data for testing');
            }

            // Set CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Additional check for DOM readiness
            if (document.readyState !== 'complete' && document.readyState !== 'interactive') {
                console.log('⏳ Waiting for DOM to be ready...');
                setTimeout(initializeCalendar, 50);
                return;
            }

            // Initialize main calendar
            const calendarEl = document.getElementById('calendar');
            if (!calendarEl) {
                console.error('Calendar element not found');
                return;
            }

            console.log('🔧 Initializing FullCalendar with', eventsData.length, 'events');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: ''
                },
                locale: 'id',
                firstDay: 1, // Monday
                height: 'auto',
                events: eventsData,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                weekends: true,
                editable: true,
                droppable: false,

                // Event handlers
                select: function(info) {
                    // Open modal for new event
                    openEventModal(null, info.startStr, info.endStr);
                    calendar.unselect();
                },

                eventClick: function(info) {
                    // Open detail modal instead of edit
                    openDetailModal(info.event);
                },

                // Custom event rendering
                eventDidMount: function(info) {
                    // Add tooltip
                    info.el.setAttribute('title', info.event.title + '\n' +
                        (info.event.extendedProps.location || '') + '\n' +
                        (info.event.extendedProps.description || ''));
                }
            });

            try {
                calendar.render();
                console.log('✅ Main calendar rendered successfully');

                // Initialize all event handlers after successful render
                initializeEventHandlers();
            } catch (error) {
                console.error('❌ Error rendering main calendar:', error);
                return;
            }

            function initializeEventHandlers() {
                console.log('🔗 Initializing event handlers...');

                // View change handlers
                const monthViewBtn = document.getElementById('monthView');
                const weekViewBtn = document.getElementById('weekView');
                const dayViewBtn = document.getElementById('dayView');
                const todayBtn = document.getElementById('todayBtn');

                if (monthViewBtn) {
                    monthViewBtn.addEventListener('click', function() {
                        calendar.changeView('dayGridMonth');
                        updateActiveView('monthView');
                    });
                }

                if (weekViewBtn) {
                    weekViewBtn.addEventListener('click', function() {
                        calendar.changeView('timeGridWeek');
                        updateActiveView('weekView');
                    });
                }

                if (dayViewBtn) {
                    dayViewBtn.addEventListener('click', function() {
                        calendar.changeView('timeGridDay');
                        updateActiveView('dayView');
                    });
                }

                if (todayBtn) {
                    todayBtn.addEventListener('click', function() {
                        calendar.today();
                    });
                }

                // Set initial active view
                updateActiveView('monthView');

                // Save event handler
                const saveBtn = document.getElementById('saveEventBtn');
                if (saveBtn) {
                    saveBtn.addEventListener('click', handleSaveEvent);
                }

                // Delete event handler
                const deleteBtn = document.getElementById('deleteEventBtn');
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', handleDeleteEvent);
                }

                // Calendar filter functionality
                document.querySelectorAll('.form-check-input').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        filterEvents();
                    });
                });

                // Mobile sidebar functionality
                initializeMobileSidebar();

                // Keyboard shortcuts
                initializeKeyboardShortcuts();

                console.log('✅ All event handlers initialized');
            }

            // Mobile sidebar functionality
            function initializeMobileSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('overlay');
                const sidebarToggle = document.getElementById('sidebarToggle');
                const closeSidebar = document.getElementById('closeSidebar');

                if (sidebarToggle && sidebar && overlay) {
                    sidebarToggle.addEventListener('click', function() {
                        sidebar.classList.add('show');
                        overlay.style.display = 'block';
                    });

                    function hideSidebar() {
                        sidebar.classList.remove('show');
                        overlay.style.display = 'none';
                    }

                    if (closeSidebar) {
                        closeSidebar.addEventListener('click', hideSidebar);
                    }
                    overlay.addEventListener('click', hideSidebar);
                }
            }

            // Keyboard shortcuts
            function initializeKeyboardShortcuts() {
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        try {
                            $('#eventModal').modal('hide');
                        } catch (error) {
                            console.error('Error hiding modal with keyboard:', error);
                        }
                    }

                    if (e.ctrlKey || e.metaKey) {
                        switch (e.key) {
                            case 'n':
                                e.preventDefault();
                                openEventModal();
                                break;
                            case 't':
                                e.preventDefault();
                                calendar.today();
                                break;
                        }
                    }
                });
            }

            // Initialize mini calendar
            const miniCalendarEl = document.getElementById('mini-calendar');
            if (!miniCalendarEl) {
                console.error('Mini calendar element not found');
                return;
            }

            const miniCalendar = new FullCalendar.Calendar(miniCalendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                height: 250,
                dayMaxEvents: false,
                showNonCurrentDates: false,
                fixedWeekCount: false,

                dateClick: function(info) {
                    try {
                        // Navigate main calendar to clicked date
                        calendar.gotoDate(info.date);
                        calendar.changeView('dayGridDay');
                    } catch (error) {
                        console.error('Error navigating calendar:', error);
                    }
                }
            });

            try {
                miniCalendar.render();

                // Auto-sync mini calendar with main calendar
                calendar.on('datesSet', function() {
                    try {
                        miniCalendar.gotoDate(calendar.getDate());
                    } catch (error) {
                        console.error('Error syncing mini calendar:', error);
                    }
                });

                console.log('✅ Mini calendar rendered and synced successfully');
            } catch (error) {
                console.error('Error rendering mini calendar:', error);
            }

            // Update active view button function
            function updateActiveView(activeId) {
                document.querySelectorAll('.view-buttons .btn').forEach(btn => {
                    btn.classList.remove('active', 'btn-primary');
                    btn.classList.add('btn-outline-secondary');
                });
                const activeBtn = document.getElementById(activeId);
                if (activeBtn) {
                    activeBtn.classList.remove('btn-outline-secondary');
                    activeBtn.classList.add('btn-primary', 'active');
                }
            }

            // Event modal functionality
            const eventModal = document.getElementById('eventModal');
            const eventForm = document.getElementById('eventForm');
            let currentEvent = null;

            function openEventModal(event = null, startDate = null, endDate = null) {
                currentEvent = event;

                if (event) {
                    // Edit mode
                    document.getElementById('eventModalTitle').textContent = 'Edit Rapat';
                    document.getElementById('eventId').value = event.id;
                    document.getElementById('eventTitle').value = event.title;

                    // Parse start date and time
                    const startDateTime = new Date(event.start);
                    document.getElementById('eventDate').value = startDateTime.toISOString().split('T')[0];
                    document.getElementById('eventTime').value = startDateTime.toTimeString().substr(0, 5);

                    document.getElementById('eventLocation').value = event.extendedProps.location || '';

                    // Map category back to database values
                    const categoryMapping = {
                        'paripurna': 'rapat_paripurna',
                        'komisi': 'rapat_komisi',
                        'badan': 'rapat_badan',
                        'lain_lain': 'lain_lain'
                    };
                    const dbCategory = categoryMapping[event.extendedProps.category] || 'rapat_paripurna';
                    document.getElementById('eventCategory').value = dbCategory;

                    document.getElementById('eventDescription').value = event.extendedProps.description || '';
                    document.getElementById('deleteEventBtn').style.display = 'inline-block';
                } else {
                    // Add mode
                    document.getElementById('eventModalTitle').textContent = 'Tambah Rapat Baru';
                    eventForm.reset();
                    document.getElementById('eventId').value = '';

                    if (startDate) {
                        const date = new Date(startDate);
                        document.getElementById('eventDate').value = date.toISOString().split('T')[0];
                        document.getElementById('eventTime').value = '09:00';
                    }

                    document.getElementById('deleteEventBtn').style.display = 'none';
                }

                try {
                    $('#eventModal').modal('show');
                } catch (error) {
                    console.error('Error showing modal:', error);
                }
            }

            function formatDateForInput(dateString) {
                const date = new Date(dateString);
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                return `${year}-${month}-${day}T${hours}:${minutes}`;
            }

            // Save event handler function
            function handleSaveEvent() {
                const eventForm = document.getElementById('eventForm');
                if (!eventForm.checkValidity()) {
                    eventForm.reportValidity();
                    return;
                }

                const eventId = document.getElementById('eventId').value;
                const formData = {
                    judul: document.getElementById('eventTitle').value,
                    tanggal: document.getElementById('eventDate').value,
                    waktu: document.getElementById('eventTime').value,
                    lokasi: document.getElementById('eventLocation').value,
                    jenis_rapat: document.getElementById('eventCategory').value,
                    deskripsi: document.getElementById('eventDescription').value
                };

                const isEdit = eventId && eventId !== '';
                const url = isEdit ?
                    `/dashboard/rapat/${eventId}` :
                    '/dashboard/rapat';
                const method = isEdit ? 'PUT' : 'POST';

                // Show loading state
                const saveBtn = document.getElementById('saveEventBtn');
                const originalText = saveBtn.textContent;
                saveBtn.textContent = 'Menyimpan...';
                saveBtn.disabled = true;

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    success: function(response) {
                        console.log('✅ Event saved successfully:', response);

                        // Reload page to refresh calendar data
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Error saving event:', error);
                        alert('Terjadi kesalahan saat menyimpan rapat: ' +
                            (xhr.responseJSON?.message || error));
                    },
                    complete: function() {
                        // Restore button state
                        saveBtn.textContent = originalText;
                        saveBtn.disabled = false;
                    }
                });
            }

            // Delete event handler function
            function handleDeleteEvent() {
                if (currentEvent && confirm('Apakah Anda yakin ingin menghapus rapat ini?')) {
                    const eventId = currentEvent.id;

                    // Show loading state
                    const deleteBtn = document.getElementById('deleteEventBtn');
                    const originalText = deleteBtn.textContent;
                    deleteBtn.textContent = 'Menghapus...';
                    deleteBtn.disabled = true;

                    $.ajax({
                        url: `/dashboard/rapat/${eventId}`,
                        method: 'DELETE',
                        success: function(response) {
                            console.log('✅ Event deleted successfully:', response);

                            // Remove from calendar
                            currentEvent.remove();

                            try {
                                $('#eventModal').modal('hide');
                            } catch (error) {
                                console.error('Error hiding modal:', error);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('❌ Error deleting event:', error);
                            alert('Terjadi kesalahan saat menghapus rapat: ' +
                                (xhr.responseJSON?.message || error));
                        },
                        complete: function() {
                            // Restore button state
                            deleteBtn.textContent = originalText;
                            deleteBtn.disabled = false;
                        }
                    });
                }
            }



            // Filter events function
            function filterEvents() {
                const showParipurna = document.getElementById('calendar1').checked;
                const showKomisi = document.getElementById('calendar2').checked;
                const showBadan = document.getElementById('calendar3').checked;
                const showLainLain = document.getElementById('calendar4').checked;

                calendar.getEvents().forEach(event => {
                    const category = event.extendedProps.category;
                    let shouldShow = false;

                    if (category === 'paripurna' && showParipurna) shouldShow = true;
                    if (category === 'komisi' && showKomisi) shouldShow = true;
                    if (category === 'badan' && showBadan) shouldShow = true;
                    if (category === 'lain_lain' && showLainLain) shouldShow = true;

                    event.setProp('display', shouldShow ? 'auto' : 'none');
                });
            }



            // Auto-refresh mini calendar when main calendar changes
            try {
                calendar.on('datesSet', function() {
                    try {
                        miniCalendar.gotoDate(calendar.getDate());
                    } catch (error) {
                        console.error('Error syncing mini calendar:', error);
                    }
                });
            } catch (error) {
                console.error('Error setting up calendar sync:', error);
            }

            // Global variables for modal handling
            window.currentEventForEdit = null;

            // Detail Modal Function
            window.openDetailModal = function(event) {
                // Populate detail modal
                document.getElementById('detailModalTitle').textContent = event.title;
                document.getElementById('detailTitle').textContent = event.title;

                // Format date and time
                const startDate = new Date(event.start);
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                };
                document.getElementById('detailDate').textContent = startDate.toLocaleDateString('id-ID', options);
                document.getElementById('detailTime').textContent = startDate.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                document.getElementById('detailLocation').textContent = event.extendedProps.location || '-';

                // Format category name
                const categoryNames = {
                    'paripurna': 'Rapat Paripurna',
                    'komisi': 'Rapat Komisi',
                    'badan': 'Rapat Badan',
                    'lain_lain': 'Lain-lainnya'
                };
                document.getElementById('detailCategory').textContent =
                    categoryNames[event.extendedProps.category] || 'Tidak diketahui';

                document.getElementById('detailDescription').textContent =
                    event.extendedProps.description || 'Tidak ada deskripsi';

                // Populate peserta list
                const pesertaContainer = document.getElementById('detailPeserta');
                if (event.extendedProps.peserta && event.extendedProps.peserta.length > 0) {
                    let pesertaHtml = '<div class="mb-2"><strong>Jumlah Peserta: ' +
                        event.extendedProps.peserta_count + '</strong></div>';

                    event.extendedProps.peserta.forEach((peserta, index) => {
                        pesertaHtml += `
                            <div class="d-flex align-items-center mb-2 p-2 border-bottom">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" 
                                     style="width: 40px; height: 40px; font-size: 14px;">
                                    ${peserta.name.charAt(0)}
                                </div>
                                <div>
                                    <div class="font-weight-bold">${peserta.name}</div>
                                    <small class="text-muted">${peserta.jabatan || 'Anggota'}</small>
                                </div>
                            </div>
                        `;
                    });
                    pesertaContainer.innerHTML = pesertaHtml;
                } else {
                    pesertaContainer.innerHTML = `
                        <div class="text-center text-muted p-4">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <p>Belum ada peserta terdaftar</p>
                        </div>
                    `;
                }

                // Store current event for edit function
                window.currentEventForEdit = event;

                // Show modal
                $('#detailModal').modal('show');
            };

            // Edit function from detail modal
            window.editRapat = function() {
                $('#detailModal').modal('hide');

                // Wait for detail modal to close, then open edit modal
                setTimeout(() => {
                    if (window.currentEventForEdit) {
                        openEventModal(window.currentEventForEdit);
                    }
                }, 300);
            };

            console.log('✅ Calendar initialized successfully!');
            console.log('📅 Sample events loaded:', eventsData.length);
            console.log('⌨️ Keyboard shortcuts: Ctrl+N (new event), Ctrl+T (today), Esc (close modal)');
        }

        // Fallback initialization if DOMContentLoaded already fired
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeCalendar);
        } else {
            initializeCalendar();
        }
    </script>
@endpush
