<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">E-Notulen</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Master
    </div>

    <li class="nav-item {{ request()->is('rapat') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('rapat') }}">
            <i class="fas fa-fw fa-handshake"></i>
            <span>Penjadwalan Rapat</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('ketersediaan-pribadi') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('ketersediaan-pribadi') }}">
            <i class="fas fa-fw fa-user-clock"></i>
            <span>Ketersediaan Pribadi</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('logistik') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('logistik') }}">
            <i class="fas fa-fw fa-box"></i>
            <span>Logistik</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('notulensi') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('notulensi') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Notulensi</span>
        </a>
    </li>

    @if (in_array(Auth::user()->role->name, ['pimpinan', 'anggota']))
        <li class="nav-item {{ request()->is('tindak-lanjut-rapat') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('tindak-lanjut-rapat.index') }}">
                <i class="fas fa-fw fa-tasks"></i>
                <span>Tindak Lanjut Rapat</span>
            </a>
        </li>
    @endif

    @if (Auth::user()->role->name == 'sekretariat')
        <li class="nav-item {{ request()->is('users') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('users.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>Manajemen User</span>
            </a>
        </li>
    @endif
    @if (Auth::user()->role->name == 'pimpinan')
        <div class="sidebar-heading">
            Approval
        </div>
        <li class="nav-item {{ request()->is('approval/approve-rapat') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('approval/approve-rapat') }}">
                <i class="fas fa-fw fa-check"></i>
                <span>Approve Rapat</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('approval/approve-notulen') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('approval/approve-notulen') }}">
                <i class="fas fa-fw fa-check-double"></i>
                <span>Approve Notulen</span>
            </a>
        </li>
        <li class="nav-item {{ request()->is('approval/approve-logistik') ? 'active' : '' }}">
            <a class="nav-link" href="{{ url('approval/approve-logistik') }}">
                <i class="fas fa-fw fa-truck"></i>
                <span>Approve Logistik</span>
            </a>
        </li>
    @endif
</ul>
