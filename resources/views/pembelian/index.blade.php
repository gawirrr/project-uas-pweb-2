@extends('layouts.app')
@section('title', 'Data Pembelian')

@section('content')
    <h1>Data Pembelian</h1>
    <a href="{{ route('pembelian.create') }}">+ Tambah Pembelian</a>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembelians as $pembelian)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pembelian->no_faktur }}</td>
                    <td>{{ $pembelian->tanggal }}</td>
                    <td>{{ $pembelian->supplier->nama }}</td>
                    <td>Rp{{ number_format($pembelian->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada data pembelian.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
