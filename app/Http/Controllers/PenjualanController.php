<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi penjualan yang sudah ada.
     */
    public function index()
    {
        // Mengambil data penjualan diurutkan dari yang terbaru, dengan relasi detail dan barang
        $penjualans = Penjualan::with('details.barang')->latest()->paginate(10);
        return view('penjualan.index', compact('penjualans'));
    }

    /**
     * Menampilkan halaman Point of Sale (POS) untuk membuat transaksi baru.
     */
    public function create()
    {
        return view('penjualan.pos');
    }

    /**
     * Menyimpan transaksi penjualan baru ke database.
     * Ini adalah endpoint yang akan dipanggil oleh JavaScript dari halaman POS.
     */
    public function store(Request $request)
    {
        // Validasi data yang diterima dari frontend
        $request->validate([
            'pelanggan' => 'nullable|string|max:255',
            'cart'      => 'required|array|min:1',
            'cart.*.id' => 'required|integer|exists:barangs,id',
            'cart.*.jumlah' => 'required|integer|min:1',
        ]);

        $cart = $request->cart;

        // Memulai transaksi database untuk menjaga integritas data
        DB::beginTransaction();
        try {
            $grandTotal = 0;
            $barangDetails = [];

            // Kunci semua baris barang yang terlibat untuk mencegah race condition
            $barangIds = array_column($cart, 'id');
            $barangs = Barang::whereIn('id', $barangIds)->lockForUpdate()->get()->keyBy('id');

            // Proses setiap item di keranjang
            foreach ($cart as $item) {
                $barang = $barangs->get($item['id']);

                // Periksa apakah stok mencukupi
                if (!$barang || $barang->stok < $item['jumlah']) {
                    // Jika stok tidak cukup, batalkan transaksi dan kirim error
                    DB::rollBack();
                    return response()->json([
                        'message' => 'Stok untuk barang "' . ($barang->nama_barang ?? 'N/A') . '" tidak mencukupi.'
                    ], 422); // 422 Unprocessable Entity
                }

                $subtotal = $barang->harga * $item['jumlah'];
                $grandTotal += $subtotal;

                // Siapkan data untuk detail penjualan
                $barangDetails[] = [
                    'barang_id' => $barang->id,
                    'jumlah' => $item['jumlah'],
                    'harga' => $barang->harga,
                    'subtotal' => $subtotal,
                ];

                // Kurangi stok barang
                $barang->stok -= $item['jumlah'];
                $barang->save();
            }

            // Buat record penjualan utama
            $penjualan = Penjualan::create([
                'nomor_invoice' => 'INV-' . time() . '-' . rand(100, 999),
                'pelanggan' => $request->pelanggan ?? 'Umum',
                'total_harga' => $grandTotal,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Buat record detail penjualan untuk setiap barang
            $penjualan->details()->createMany($barangDetails);

            // Jika semua berhasil, konfirmasi transaksi
            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil disimpan!',
                'penjualan_id' => $penjualan->id // Kirim ID untuk redirect atau cetak struk
            ], 201); // 201 Created

        } catch (\Exception $e) {
            // Jika terjadi error, batalkan semua perubahan
            DB::rollBack();

            // Kirim response error ke frontend
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses transaksi.',
                'error' => $e->getMessage()
            ], 500); // 500 Internal Server Error
        }
    }

    /**
     * Menampilkan detail dari satu transaksi penjualan.
     */
    public function show(Penjualan $penjualan)
    {
        // Eager load relasi untuk efisiensi query
        $penjualan->load('details.barang');
        return view('penjualan.show', compact('penjualan'));
    }

    /**
     * Endpoint API untuk mencari barang (digunakan oleh JavaScript di halaman POS).
     */
    public function searchProducts(Request $request)
    {
        $query = $request->input('query', '');
        $barangs = Barang::where('nama_barang', 'LIKE', "%{$query}%")
            ->where('stok', '>', 0) // Hanya tampilkan barang yang ada stok
            ->take(10)
            ->get(['id', 'nama_barang', 'harga', 'stok']);

        return response()->json($barangs);
    }
}
