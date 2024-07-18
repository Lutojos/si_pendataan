<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-warning elevation-4">
    <!-- Brand Logo -->
    <div class="text-center">
        <a href="{{ url('/') }}" class="brand-link">
            {{-- <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> --}}
            <span class="brand-text font-weight">{{ env('APP_NAME') }}</span>
        </a>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ auth()->user()->getAvatar() }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="{{ url('/') }}" class="d-block">{{ \Auth::user()->name }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                id="mainMenuTreeview" data-accordion="true">
                <li class="nav-item">
                    <a href="{{ route('dashboard.index') }}"
                        class="nav-link {{ Route::is('dashboard.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item {{ request()->is('master-data*') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="javascript:void(0)" class="nav-link {{ request()->is('master-data*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-th"></i>
                        <p>Master Data
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('list provinsi')
                            <li class="nav-item">
                                <a href="{{ route('provinsi.index') }}"
                                    class="nav-link {{ Route::is('provinsi.index') ? 'active' : '' }}">
                                    <i class="av-icon fas fa-hotel nav-icon"></i>
                                    <p>Provinsi</p>
                                </a>
                            </li>
                        @endcan
                        @can('list kota')
                            <li class="nav-item">
                                <a href="{{ route('kota.index') }}"
                                    class="nav-link {{ Route::is('kota.index') ? 'active' : '' }}">
                                    <i class="av-icon fas fa-door-open nav-icon"></i>
                                    <p>Kota</p>
                                </a>
                            </li>
                        @endcan
                        @can('list kecamatan')
                            <li class="nav-item">
                                <a href="{{ route('kecamatan.index') }}"
                                    class="nav-link {{ Route::is('kecamatan.index') ? 'active' : '' }}">
                                    <i class="av-icon fas fa-clipboard nav-icon"></i>
                                    <p>Kecamatan</p>
                                </a>
                            </li>
                        @endcan
                        @can('list desa')
                            <li class="nav-item">
                                <a href="{{ route('desa.index') }}"
                                    class="nav-link {{ Route::is('desa.index') ? 'active' : '' }}">
                                    <i class="av-icon fas fa-clipboard nav-icon"></i>
                                    <p>Desa</p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @can('list anggota')
                    <li class="nav-item">
                        <a href="{{ route('anggota.index') }}"
                            class="nav-link {{ Route::is('anggota.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-receipt"></i>
                            <p>Anggota</p>
                        </a>
                    </li>
                @endcan
                <li class="nav-item {{ request()->is('management-user*') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="javascript:void(0)"
                        class="nav-link {{ request()->is('management-user*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Management User
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('list role')
                            <li class="nav-item">
                                <a href="{{ route('role.index') }}"
                                    class="nav-link {{ Route::is('role.index') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-cog nav-icon"></i>
                                    <p>Role</p>
                                </a>
                            </li>
                        @endcan
                        @if (auth()->user()->can('list users'))
                            <li class="nav-item">
                                <a href="{{ route('user.index') }}"
                                    class="nav-link {{ Route::is('user.index') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-user-friends nav-icon"></i>
                                    <p>Staff</p>
                                </a>
                            </li>
                        @endif

                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
