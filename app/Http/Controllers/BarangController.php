<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kategori;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar semua barang.
     */
    public function index(Request $request)
    {
        // PERBAIKAN: Mengurutkan berdasarkan 'nama_kategori'
        $kategoris = Kategori::orderBy('nama_kategori')->get();
        $query = Barang::with('kategori');

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori_id')) {
            $query->where('id_kategori', $request->kategori_id);
        }

        $barangs = $query->latest()->get();

        return view('barang.index', [
            'barangs' => $barangs,
            'kategoris' => $kategoris,
            'search' => $request->search,
            'selectedKategori' => $request->kategori_id
        ]);
    }

    /**
     * Menampilkan form untuk membuat barang baru.
     */
    public function create()
    {
        return view('barang.create');
    }

    /**
     * Menyimpan barang baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        // PERBAIKAN: Mencari atau membuat kategori berdasarkan 'nama_kategori'
        $kategori = Kategori::firstOrCreate(
            ['nama_kategori' => $validatedData['kategori']]
        );

        Barang::create([
            'nama' => $validatedData['nama'],
            'id_kategori' => $kategori->id,
            'stok' => $validatedData['stok'],
            'harga_beli' => $validatedData['harga'],
            'harga_jual' => $validatedData['harga'],
        ]);

        return redirect()->route('barang.index')->with('success', 'Barang baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit barang.
     */
    public function edit(Barang $barang)
    {
        return view('barang.edit', compact('barang'));
    }

    /**
     * Mengupdate data barang di database.
     */
    public function update(Request $request, Barang $barang)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        // PERBAIKAN: Mencari atau membuat kategori berdasarkan 'nama_kategori'
        $kategori = Kategori::firstOrCreate(
            ['nama_kategori' => $validatedData['kategori']]
        );

        $barang->update([
            'nama' => $validatedData['nama'],
            'id_kategori' => $kategori->id,
            'stok' => $validatedData['stok'],
            'harga_beli' => $validatedData['harga'],
            'harga_jual' => $validatedData['harga'],
        ]);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui!');
    }

    /**
     * Menghapus data barang dari database.
     */
    public function destroy(Barang $barang)
    {
        try {
            $barang->delete();
            return redirect()->route('barang.index')->with('success', 'Data barang berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('barang.index')->with('error', 'Gagal menghapus barang. Mungkin sudah ada transaksi terkait.');
        }
    }
}