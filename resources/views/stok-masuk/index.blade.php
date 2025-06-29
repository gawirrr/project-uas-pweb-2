@extends('layout')
@section('content')
<h2>Stok Masuk</h2>
<form action="/stok-masuk" method="POST">
    @csrf
    <label>Barang ID:</label> <input type="number" name="barang_id"><br>
    <label>Jumlah:</label> <input type="number" name="jumlah"><br>
    <label>Tanggal:</label> <input type="date" name="tanggal"><br>
    <label>Keterangan:</label> <textarea name="keterangan"></textarea><br>
    <button type="submit">Tambah</button>
</form>
@endsection
