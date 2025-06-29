@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 font-bold text-xl">Dashboard</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-4 bg-white rounded-xl shadow">
            <p class="text-gray-500">Pendapatan Hari Ini</p>
            <h3 class="text-xl font-bold text-green-600">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
        </div>
        
        {{-- Kartu untuk Total Pengeluaran --}}
        <div class="p-4 bg-white rounded-xl shadow">
            <p class="text-gray-500">Total Pengeluaran</p>
            <h3 class="text-xl font-bold text-red-600">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
        </div>

        <div class="p-4 bg-white rounded-xl shadow">
            <p class="text-gray-500">Transaksi Hari Ini</p>
            <h3 class="text-xl font-bold text-blue-600">{{ $transaksiHariIni }}</h3>
        </div>
        
        <div class="p-4 bg-white rounded-xl shadow">
            <p class="text-gray-500">Total Produk</p>
            <h3 class="text-xl font-bold text-yellow-600">{{ $totalProduk }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="md:col-span-2 p-4 bg-white rounded-xl shadow">
            <h3 class="mb-2 font-semibold">Grafik Penjualan (7 Hari Terakhir)</h3>
            <canvas id="penjualanChart" height="120"></canvas>
        </div>
        <div class="p-4 bg-white rounded-xl shadow">
            <h3 class="mb-2 font-semibold">Aktivitas Terakhir</h3>
            <ul class="space-y-2 text-sm">
                @forelse($logAktivitas as $log)
                    <li class="flex justify-between items-center border-b pb-1">
                        <div>
                            <span>{{ $log['keterangan'] }}</span>
                            <small class="block text-gray-400">{{ \Carbon\Carbon::parse($log['waktu'])->diffForHumans() }}</small>
                        </div>
                        @if($log['nominal'])
                            <span class="font-semibold {{ $log['arah'] == 'naik' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $log['arah'] == 'naik' ? '+' : '-' }} Rp {{ number_format($log['nominal'], 0, ',', '.') }}
                            </span>
                        @endif
                    </li>
                @empty
                    <li class="text-gray-500">Belum ada aktivitas.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // PERBAIKAN: Menggunakan variabel yang sudah diolah dari controller
    const labels = {!! json_encode($chartLabels) !!};
    const data = {!! json_encode($chartData) !!};

    const ctx = document.getElementById('penjualanChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
