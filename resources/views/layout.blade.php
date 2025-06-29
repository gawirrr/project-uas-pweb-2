<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Toko Vape')</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand">
            <a href="/">
                <i class="fa-solid fa-store"></i> Toko Vape
            </a>
        </div>
        <ul class="navbar-links">
            <li><a href="#"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li class="dropdown">
                <a href="#"><i class="fa-solid fa-box-archive"></i> Master Data <i class="fa-solid fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="#">Data Produk</a>
                    <a href="#">Data Supplier</a>
                    <a href="#">Data Pelanggan</a>
                </div>
            </li>
             <li class="dropdown">
                <a href="#"><i class="fa-solid fa-right-left"></i> Transaksi <i class="fa-solid fa-caret-down"></i></a>
                <div class="dropdown-content">
                    <a href="{{ route('pembelian.create') }}">Input Pembelian</a>
                    <a href="#">Input Penjualan</a>
                </div>
            </li>
        </ul>
        <div class="navbar-user">
            <a href="#">
                <i class="fa-solid fa-user"></i>
                Nama User
            </a>
        </div>
    </nav>

    <div class="main-content">
        @if(session('success'))
            {{-- Kita bisa buat style khusus untuk alert nanti --}}
            <div style="padding: 1rem; margin-bottom: 1rem; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px;">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>

    {{-- Kita hapus script Bootstrap karena sudah tidak digunakan --}}
</body>
</html>