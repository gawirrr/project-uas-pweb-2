<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        // --- Data Kartu Statistik ---
        $pendapatanHariIni = Penjualan::whereDate('created_at', Carbon::today())->sum('total_harga');
        $transaksiHariIni = Penjualan::whereDate('created_at', Carbon::today())->count();
        $totalProduk = Barang::count();
        $totalPengeluaran = Pembelian::sum('total'); // Menghitung total pengeluaran

        // --- Logika Grafik Penjualan yang Diperbaiki ---

        // 1. Ambil data penjualan aktual dari 7 hari terakhir
        $penjualanData = Penjualan::selectRaw('DATE(created_at) as tanggal, SUM(total_harga) as total')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal'); // Membuat array asosiatif [tanggal => total]

        // 2. Buat rentang tanggal 7 hari terakhir sebagai dasar, isi dengan nilai 0
        $tanggalRange = new Collection();
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i)->format('Y-m-d');
            $tanggalRange->put($tanggal, 0);
        }

        // 3. Gabungkan data aktual ke rentang tanggal dasar
        $dataGrafik = $tanggalRange->merge($penjualanData);

        // 4. Siapkan label dan data yang siap pakai untuk Chart.js
        $chartLabels = $dataGrafik->keys()->map(fn($t) => Carbon::parse($t)->format('d M'));
        $chartData = $dataGrafik->values();


        // --- Logika Aktivitas Terakhir ---
        $logPenjualan = Penjualan::latest()->take(3)->get()->map(function ($item) {
            return [
                'keterangan' => "Penjualan #INV{$item->id}",
                'nominal' => $item->total_harga,
                'waktu' => $item->created_at,
                'arah' => 'naik'
            ];
        });

        $logPembelian = Pembelian::with('supplier')->latest()->take(3)->get()->map(function ($item) {
            return [
                'keterangan' => "Pembelian dari {$item->supplier->nama}",
                'nominal' => $item->total,
                'waktu' => $item->created_at,
                'arah' => 'turun'
            ];
        });

        $logAktivitas = collect($logPenjualan)
            ->merge($logPembelian)
            ->sortByDesc('waktu')
            ->take(5);

        // --- Kirim semua data yang sudah diolah ke view ---
        return view('dashboard', [
            'pendapatanHariIni' => $pendapatanHariIni,
            'transaksiHariIni' => $transaksiHariIni,
            'totalProduk' => $totalProduk,
            'totalPengeluaran' => $totalPengeluaran,
            'chartLabels' => $chartLabels, // Mengirim label yang sudah jadi
            'chartData' => $chartData,     // Mengirim data yang sudah jadi
            'logAktivitas' => $logAktivitas
        ]);
    }
}
