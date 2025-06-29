@extends('layouts.app')
@section('title', 'Tambah Barang Baru')

@section('content')
    <div class="page-header">
        <h1>
            <i class="fa-solid fa-plus-circle"></i>
            Tambah Barang Baru
        </h1>
        <a href="{{ route('barang.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    {{-- Menampilkan SEMUA pesan error di bagian atas --}}
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; padding: 1rem; border-radius: 6px;">
            <strong>Oops! Ada yang salah dengan input Anda.</strong>
            <ul style="margin-top: 0.5rem; margin-left: 1rem; padding-left: 1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('barang.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="nama">Nama Barang</label>
                {{-- 
                    PERBAIKAN KUNCI: 
                    'name' diubah dari 'nama_barang' menjadi 'nama' agar cocok dengan controller.
                    Juga ditambahkan value="{{ old('nama') }}" untuk menyimpan input jika validasi gagal.
                --}}
                <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                
                {{-- Menampilkan pesan error spesifik untuk 'nama' --}}
                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Kategori</label>
                <div class="radio-group-container @error('kategori') is-invalid @enderror">
                    @php
                        $kategoriOptions = ['Device', 'Catridge', 'Liquid', 'Coil', 'Kapas', 'Baterai'];
                    @endphp
                    @foreach($kategoriOptions as $kategori)
                    <div class="radio-option">
                        <input type="radio" id="kategori_{{ strtolower($kategori) }}" name="kategori" value="{{ $kategori }}" {{ old('kategori') == $kategori ? 'checked' : '' }} required>
                        <label for="kategori_{{ strtolower($kategori) }}">{{ $kategori }}</label>
                    </div>
                    @endforeach
                </div>
                @error('kategori')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" id="harga" name="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga') }}" required>
                @error('harga')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="stok">Stok Awal</label>
                <input type="number" id="stok" name="stok" class="form-control @error('stok') is-invalid @enderror" value="{{ old('stok') }}" required>
                @error('stok')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" style="text-align: right; border-top: 1px solid #f0f2f5; padding-top: 1.5rem; margin-top: 2rem; margin-bottom: 0;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Simpan Barang
                </button>
            </div>
        </form>
    </div>

    {{-- CSS untuk menyorot input yang error --}}
    <style>
        .is-invalid { border-color: #e53e3e !important; }
        .invalid-feedback { color: #e53e3e; font-size: 0.875rem; margin-top: 0.25rem; }
        .radio-group-container.is-invalid { border: 1px solid #e53e3e; border-radius: 6px; padding: 0.5rem; }
    </style>
@endsection