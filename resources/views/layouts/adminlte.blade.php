<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — Toko Bahan Bangunan</title>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- AdminLTE CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css">
    {{-- Vite (Bootstrap SCSS) --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">

    <div class="app-wrapper">

        {{-- Navbar --}}
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-bs-toggle="dropdown" href="#">
                            <i class="bi bi-person-circle fs-5"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="dropdown-header">
                                <strong>{{ Auth::user()->name }}</strong><br>
                                <small class="text-muted text-capitalize">{{ Auth::user()->role }}</small>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        {{-- Sidebar --}}
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="{{ url('/') }}" class="brand-link">
                    <span class="brand-text fw-light">
                        <i class="bi bi-building me-1"></i> Toko BB
                    </span>
                </a>
            </div>

            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu">

                        {{-- ========================================== --}}
                        {{-- MENU KHUSUS ADMIN --}}
                        {{-- ========================================== --}}
                        @if(Auth::user()->role === 'admin')

                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-speedometer2"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>

                            <!-- ================= HEADER: MASTER DATA ================= -->
                            <li class="nav-header">MASTER DATA</li>

                            <li
                                class="nav-item has-treeview {{ request()->routeIs('admin.produk*', 'admin.kategori*', 'admin.merek*', 'admin.satuan*', 'admin.konversi*') ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ request()->routeIs('admin.produk*', 'admin.kategori*', 'admin.merek*', 'admin.satuan*', 'admin.konversi*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-box-seam"></i>
                                    <p>Produk & Stok <i class="nav-arrow bi bi-chevron-right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.produk.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.produk*') ? 'active' : '' }}">
                                            <i class="nav-icon bi bi-dot"></i>
                                            <p>Daftar Produk</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.kategori.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.kategori*') ? 'active' : '' }}">
                                            <i class="nav-icon bi bi-dot"></i>
                                            <p>Kategori</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.merek.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.merek*') ? 'active' : '' }}">
                                            <i class="nav-icon bi bi-dot"></i>
                                            <p>Merek</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.satuan.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.satuan*') ? 'active' : '' }}">
                                            <i class="nav-icon bi bi-dot"></i>
                                            <p>Satuan</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.konversi.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.konversi*') ? 'active' : '' }}">
                                            <i class="nav-icon bi bi-dot"></i>
                                            <p>Master Rumus Konversi</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.supplier.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.supplier*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-truck"></i>
                                    <p>Supplier</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.customer.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.customer*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-people"></i>
                                    <p>Customer</p>
                                </a>
                            </li>

                            <!-- ================= HEADER: PEMBELIAN ================= -->
                            <li class="nav-header">PEMBELIAN</li>

                            <li
                                class="nav-item has-treeview {{ request()->routeIs('admin.pembelian*', 'admin.pembayaran-hutang*') ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ request()->routeIs('admin.pembelian*', 'admin.pembayaran-hutang*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-cart-plus"></i>
                                    <p>
                                        Menu Pembelian
                                        <i class="nav-arrow bi bi-chevron-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.pembelian.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.pembelian.index', 'admin.pembelian.create', 'admin.pembelian.show') ? 'active' : '' }}">
                                            <i class="nav-icon bi bi-dot"></i>
                                            <p>Order Pembelian (PO)</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.pembelian.penerimaan.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.pembelian.penerimaan*') ? 'active' : '' }}">
                                            <i class="nav-icon bi bi-dot"></i>
                                            <p>Penerimaan Barang</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.pembayaran-hutang.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.pembayaran-hutang*') ? 'active' : '' }}">
                                            <i class="nav-icon bi bi-dot"></i>
                                            <p>Pembayaran Hutang (Tempo)</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- ================= HEADER: PENJUALAN ================= -->
                            <li class="nav-header">PENJUALAN</li>

                            <li
                                class="nav-item has-treeview {{ request()->routeIs('admin.penjualan*', 'admin.piutang-customer*') ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ request()->routeIs('admin.penjualan*', 'admin.piutang-customer*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-cash-coin"></i>
                                    <p>
                                        Menu Penjualan
                                        <i class="nav-arrow bi bi-chevron-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('admin.penjualan.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.penjualan.index', 'admin.penjualan.create', 'admin.penjualan.show') ? 'active' : '' }}">
                                            <i class="nav-icon bi bi-dot"></i>
                                            <p>Order Penjualan (Nota/SO)</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.penjualan.pengiriman.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.penjualan.pengiriman*') ? 'active' : '' }}">
                                            <i class="nav-icon bi bi-dot"></i>
                                            <p>Pengiriman Barang</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('admin.piutang-customer.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.piutang-customer*') ? 'active' : '' }}">
                                            <i class="nav-icon bi bi-dot"></i>
                                            <p>Piutang Pelanggan (Tempo)</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <!-- ================= HEADER: INVENTORI & STOK ================= -->
                            <li class="nav-header">INVENTORI & STOK</li>

                            <li class="nav-item">
                                <a href="{{ route('admin.stok-opname.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.stok-opname*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-clipboard-check"></i>
                                    <p>Stok Opname</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.konversi-produk.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.konversi-produk*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-arrow-repeat"></i>
                                    <p>Konversi Produk</p>
                                </a>
                            </li>

                            <!-- ================= HEADER: SPK REKOMENDASI ================= -->
                            <li class="nav-header">SISTEM KEPUTUSAN (SPK)</li>

                            <li class="nav-item">
                                <a href="{{ route('admin.sesi-perhitungan-ahp.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.sesi-perhitungan-ahp*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-diagram-3"></i>
                                    <p>Bobot AHP</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.rekomendasi-supplier.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.rekomendasi-supplier*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-trophy"></i>
                                    <p>Rekomendasi SAW</p>
                                </a>
                            </li>
                        @endif

                        {{-- ========================================== --}}
                        {{-- MENU KHUSUS KASIR --}}
                        {{-- ========================================== --}}
                        @if(Auth::user()->role === 'kasir')

                            <li class="nav-item">
                                <a href="{{ route('kasir.dashboard') }}"
                                    class="nav-link {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-speedometer2"></i>
                                    <p>Dashboard Kasir</p>
                                </a>
                            </li>

                            <li class="nav-header">TRANSAKSI KASIR</li>

                            <li class="nav-item">
                                <a href="{{ route('admin.pos.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.pos*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-calculator"></i>
                                    <p>Mesin POS Kasir</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.penjualan.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.penjualan*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-receipt"></i>
                                    <p>Order Penjualan</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.customer.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.customer*') ? 'active' : '' }}">
                                    <i class="nav-icon bi bi-people"></i>
                                    <p>Data Customer</p>
                                </a>
                            </li>

                        @endif


                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">@yield('page_title', 'Dashboard')</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">

                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </main>

        <footer class="app-footer">
            <strong>Sistem Informasi Toko Bahan Bangunan</strong> &copy; {{ date('Y') }}
        </footer>

    </div>

    {{-- Bootstrap Bundle JS (Wajib untuk Modal Pop-up) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- AdminLTE JS --}}
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/js/adminlte.min.js"></script>
    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    @stack('scripts')
</body>

</html>