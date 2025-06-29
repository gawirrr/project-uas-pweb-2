<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Sistem Penjualan Toko Vape')</title>

    {{-- Link ke file CSS di folder public --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    {{-- Navbar Horizontal --}}
    <nav class="navbar">
        <div class="navbar-brand">
            <a href="{{ route('dashboard') }}"><i class="fa-solid fa-store"></i> TokoVape</a>
        </div>
        <ul class="navbar-links">
            <li><a href="{{ route('dashboard') }}"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li class="dropdown">
                <a href="#"><i class="fa-solid fa-database"></i> Master Data <i class="fa-solid fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="{{ route('barang.index') }}">Data Barang</a>
                    <a href="{{ route('supplier.index') }}">Data Supplier</a>
                </div>
            </li>
            <li class="dropdown">
                <a href="#"><i class="fa-solid fa-right-left"></i> Transaksi <i class="fa-solid fa-caret-down"></i></a>
                <div class="dropdown-content">
                   <a href="{{ route('penjualan.create') }}">Input Penjualan</a>
                    <a href="{{ route('pembelian.create') }}">Input Pembelian</a>
                </div>
            </li>
        
        </ul>
        <div class="navbar-user">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer;">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
    </form>
</div>
    </nav>

    {{-- Kontainer untuk Konten Utama --}}
    <main class="main-content">
        {{-- PASTIKAN HANYA ADA SATU BARIS INI --}}
        @yield('content')
    </main>

    {{-- Untuk menampung script dari halaman lain --}}
    @stack('scripts') 
</body>
</html>